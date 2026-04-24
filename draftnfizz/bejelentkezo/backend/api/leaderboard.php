<?php
require_once '../db.php';

header('Content-Type: application/json; charset=utf-8');

$stmt = $pdo->query("
    SELECT 
        u.id,
        u.username,
        u.profile_image,
        u.current_streak,
        COUNT(ua.id) AS credits
    FROM users u
    LEFT JOIN user_answers ua 
        ON u.id = ua.user_id 
        AND ua.is_correct = 1
    GROUP BY u.id, u.username, u.profile_image, u.current_streak
    HAVING credits > 0
    ORDER BY credits DESC
");
$leaderboard = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $leaderboard[] = [
        'id' => (int)$row['id'],
        'username' => $row['username'],
        'credits' => (int)$row['credits'],
        'current_streak' => (int)$row['current_streak'],
        'profile_image' => !empty($row['profile_image'])
                ? 'bejelentkezo/backend/' . $row['profile_image']
                : 'https://via.placeholder.com/50'
    ];
}
echo json_encode([
    'success' => true,
    'leaderboard' => $leaderboard
]);