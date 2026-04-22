<?php
require_once '../foprogram/db.php';

header('Content-Type: application/json; charset=utf-8');

$sql = "
SELECT 
    p.id,
    p.name,
    ps.attack,
    ps.controll,
    ps.defence,
    pr.rating,
    t.name AS team,
    pos.name AS position,
    tk.image_path AS shirt_image
FROM players p
JOIN teams t ON p.team_id = t.id
JOIN positions pos ON p.position_id = pos.id
LEFT JOIN player_stats ps ON ps.player_id = p.id
LEFT JOIN player_ratings pr ON pr.player_id = p.id
LEFT JOIN team_kits tk ON tk.team_id = t.id
ORDER BY p.id
";

$stmt = $pdo->query($sql);
$players = $stmt->fetchAll();

echo json_encode($players, JSON_UNESCAPED_UNICODE);
?>