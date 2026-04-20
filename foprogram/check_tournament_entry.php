<?php
require_once 'db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';

    if (!$authHeader || strpos($authHeader, 'Bearer ') !== 0) {
        throw new Exception('Be kell jelentkezni.');
    }

    $token = substr($authHeader, 7);

    // USER
    $stmt = $pdo->prepare("
        SELECT id FROM users
        WHERE token = ?
        AND token_expiry > NOW()
        LIMIT 1
    ");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('Be kell jelentkezni.');
    }

    $userId = $user['id'];

    // AKTÍV BAJNOKSÁG
    $stmt = $pdo->prepare("
        SELECT id
        FROM tournaments
        WHERE status = 'open'
        AND NOW() BETWEEN start_at AND entry_deadline
        LIMIT 1
    ");
    $stmt->execute();
    $tournament = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tournament) {
        echo json_encode([
            'success' => true,
            'joined' => false,
            'tournament_id' => null
        ]);
        exit;
    }

    $tournamentId = $tournament['id'];

    // VAN-E MÁR ENTRY?
    $stmt = $pdo->prepare("
        SELECT id
        FROM tournament_entries
        WHERE tournament_id = ? AND user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$tournamentId, $userId]);

    $exists = $stmt->fetch();

    echo json_encode([
        'success' => true,
        'joined' => $exists ? true : false,
        'tournament_id' => $tournamentId
    ]);

} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}