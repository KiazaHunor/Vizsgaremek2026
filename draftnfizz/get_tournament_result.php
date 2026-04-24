<?php
require_once 'db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $tournamentId = (int)($_GET['id'] ?? 0);

    if ($tournamentId <= 0) {
        throw new Exception("Hiányzó bajnokság ID.");
    }

    $stmt = $pdo->prepare("
        SELECT
            te.rank_position,
            u.username,
            te.team_name,
            te.chemistry_score,
            te.rating_avg_score,
            te.final_score,
            te.credits_awarded
        FROM tournament_entries te
        INNER JOIN users u ON u.id = te.user_id
        WHERE te.tournament_id = ?
        ORDER BY te.rank_position ASC
    ");

    $stmt->execute([$tournamentId]);

    echo json_encode([
        "success" => true,
        "results" => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}