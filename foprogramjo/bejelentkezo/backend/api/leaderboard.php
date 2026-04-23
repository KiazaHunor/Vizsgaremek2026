<?php
require_once '../db.php';

header('Content-Type: application/json; charset=utf-8');

$stmt = $pdo->query("
    SELECT 
        u.id, 
        u.username, 
        u.profile_image, 
        u.current_streak,
        COUNT(ua.id) as credits
    FROM users u
    LEFT JOIN user_achievements ua ON u.id = ua.user_id
    GROUP BY u.id, u.username, u.profile_image, u.current_streak
    HAVING COUNT(ua.id) > 0
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
            ? '../uploads/profile_images/' . $row['profile_image']
            : 'https://via.placeholder.com/50'
    ];
}
echo json_encode([
    'success' => true,
    'leaderboard' => $leaderboard
]);