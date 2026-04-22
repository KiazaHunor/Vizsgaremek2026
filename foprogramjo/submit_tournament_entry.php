<?php
require_once 'db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

    if (!$authHeader || strpos($authHeader, 'Bearer ') !== 0) {
        throw new Exception('Be kell jelentkezni.');
    }

    $token = trim(substr($authHeader, 7));

    if ($token === '') {
        throw new Exception('Be kell jelentkezni.');
    }

    $stmtUser = $pdo->prepare("
        SELECT id, username
        FROM users
        WHERE token = ?
          AND token_expiry IS NOT NULL
          AND token_expiry > NOW()
        LIMIT 1
    ");
    $stmtUser->execute([$token]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('Be kell jelentkezni.');
    }

    $userId = (int)$user['id'];

    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        throw new Exception('Nincs adat elküldve.');
    }

    $tournamentId = (int)($data['tournament_id'] ?? 0);
    $teamName = trim($data['team_name'] ?? '');
    $chemistryScore = (int)($data['chemistry_score'] ?? 0);
    $ratingAvgScore = (int)($data['rating_avg_score'] ?? 0);
    $finalScore = (int)($data['final_score'] ?? 0);

    if ($tournamentId <= 0) {
        throw new Exception('Hiányzó bajnokság.');
    }

    $stmt = $pdo->prepare("
        SELECT id
        FROM tournaments
        WHERE id = ?
          AND status = 'open'
          AND NOW() BETWEEN start_at AND entry_deadline
        LIMIT 1
    ");
    $stmt->execute([$tournamentId]);
    $tournament = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tournament) {
        throw new Exception('Ez a bajnokság már nem nevezhető.');
    }

    $stmt = $pdo->prepare("
        SELECT id
        FROM tournament_entries
        WHERE tournament_id = ? AND user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$tournamentId, $userId]);

    if ($stmt->fetch()) {
        throw new Exception('Erre a bajnokságra már neveztél.');
    }

    $stmt = $pdo->prepare("
        INSERT INTO tournament_entries
        (tournament_id, user_id, team_name, chemistry_score, rating_avg_score, final_score, submitted_at, is_locked)
        VALUES (?, ?, ?, ?, ?, ?, NOW(), 1)
    ");
    $stmt->execute([
        $tournamentId,
        $userId,
        $teamName,
        $chemistryScore,
        $ratingAvgScore,
        $finalScore
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Sikeres nevezés.'
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}