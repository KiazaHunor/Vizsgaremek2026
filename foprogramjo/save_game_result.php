<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/bejelentkezo/backend/api/auth.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data["result"], $data["player_score"], $data["enemy_score"])) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Hiányzó adatok!"
    ]);
    exit;
}

$currentUserId = (int)($GLOBALS['current_user_id'] ?? 0);

if ($currentUserId <= 0) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Csak bejelentkezett felhasználó statisztikája menthető."
    ]);
    exit;
}

$result = $data["result"];
$playerScore = (int)$data["player_score"];
$enemyScore = (int)$data["enemy_score"];

$allowedResults = ["win", "draw", "loss"];
if (!in_array($result, $allowedResults, true)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Érvénytelen eredmény."
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO card_game_results (user_id, result, player_score, enemy_score)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$currentUserId, $result, $playerScore, $enemyScore]);

    echo json_encode([
        "success" => true,
        "message" => "Eredmény elmentve."
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Mentési hiba történt."
    ]);
}