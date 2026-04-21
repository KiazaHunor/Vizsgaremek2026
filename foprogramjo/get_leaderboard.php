<?php
require_once 'db.php';

$tournamentId = $_GET['tournament_id'] ?? 0;

$stmt = $pdo->prepare("
    SELECT u.username, te.final_score
    FROM tournament_entries te
    JOIN users u ON te.user_id = u.id
    WHERE te.tournament_id = ?
    ORDER BY te.final_score DESC
    LIMIT 5
");

$stmt->execute([$tournamentId]);

echo json_encode($stmt->fetchAll());