<?php
require_once 'db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $tournamentId = (int)($_GET['tournament_id'] ?? 0);

    if ($tournamentId <= 0) {
        throw new Exception('Hiányzó tournament_id.');
    }

    $stmt = $pdo->prepare("
        SELECT
            te.id,
            te.user_id,
            u.username,
            te.team_name,
            te.chemistry_score,
            te.rating_avg_score,
            te.final_score,
            te.rank_position,
            te.credits_awarded,
            te.submitted_at
        FROM tournament_entries te
        INNER JOIN users u ON u.id = te.user_id
        WHERE te.tournament_id = ?
        ORDER BY
            te.final_score DESC,
            te.chemistry_score DESC,
            te.rating_avg_score DESC,
            te.submitted_at ASC
    ");
    $stmt->execute([$tournamentId]);

    echo json_encode([
        'success' => true,
        'leaderboard' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}