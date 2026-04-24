<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/bejelentkezo/backend/api/auth.php';

$currentUserId = (int)($GLOBALS['current_user_id'] ?? 0);

if ($currentUserId <= 0) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "A statisztikák megtekintéséhez jelentkezz be."
    ]);
    exit;
}

try {
    $summaryStmt = $pdo->prepare("
        SELECT result
        FROM card_game_results
        WHERE user_id = ?
    ");
    $summaryStmt->execute([$currentUserId]);
    $allResults = $summaryStmt->fetchAll(PDO::FETCH_ASSOC);

    $wins = 0;
    $draws = 0;
    $losses = 0;

    foreach ($allResults as $row) {
        if ($row['result'] === 'win') $wins++;
        if ($row['result'] === 'draw') $draws++;
        if ($row['result'] === 'loss') $losses++;
    }

    $lastFiveStmt = $pdo->prepare("
        SELECT id, result, player_score, enemy_score, played_at
        FROM card_game_results
        WHERE user_id = ?
        ORDER BY played_at DESC, id DESC
        LIMIT 5
    ");
    $lastFiveStmt->execute([$currentUserId]);
    $recentFive = $lastFiveStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "summary" => [
            "total" => count($allResults),
            "wins" => $wins,
            "draws" => $draws,
            "losses" => $losses
        ],
        "recentFive" => $recentFive
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Hiba a statisztikák lekérdezése közben."
    ]);
}