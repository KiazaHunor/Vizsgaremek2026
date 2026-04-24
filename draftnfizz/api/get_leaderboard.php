<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../db.php';

try {
    $stmt = $pdo->query("
        SELECT
            u.username AS Username,
            COUNT(ua.id) AS Score,
            u.current_streak AS CurrentStreak
        FROM users u
        LEFT JOIN user_answers ua
            ON u.id = ua.user_id AND ua.is_correct = 1
        GROUP BY u.id, u.username, u.current_streak
        ORDER BY Score DESC, u.current_streak DESC, u.username ASC
        LIMIT 50
    ");

    $leaderboard = $stmt->fetchAll();

    foreach ($leaderboard as &$item) {
        $item['Score'] = (int)$item['Score'];
        $item['CurrentStreak'] = (int)$item['CurrentStreak'];
    }

    echo json_encode([
        "success" => true,
        "leaderboard" => $leaderboard
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Szerverhiba: " . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}