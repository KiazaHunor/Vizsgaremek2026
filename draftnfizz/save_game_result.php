<?php
require_once 'db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

    if (!$authHeader || strpos($authHeader, 'Bearer ') !== 0) {
        throw new Exception('Nincs token.');
    }

    $token = trim(substr($authHeader, 7));

    $stmtUser = $pdo->prepare("
        SELECT id, credit
        FROM users
        WHERE token = ?
          AND token_expiry IS NOT NULL
          AND token_expiry > NOW()
        LIMIT 1
    ");
    $stmtUser->execute([$token]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('Érvénytelen token.');
    }

    $userId = (int)$user['id'];

    $data = json_decode(file_get_contents("php://input"), true);

    $result = $data['result'] ?? null;
    $playerScore = (int)($data['player_score'] ?? 0);
    $enemyScore = (int)($data['enemy_score'] ?? 0);

    if (!$result || !in_array($result, ['win', 'loss', 'draw'], true)) {
        throw new Exception('Hibás eredmény.');
    }

    $stmtInsert = $pdo->prepare("
        INSERT INTO card_game_results (user_id, result, player_score, enemy_score, played_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmtInsert->execute([$userId, $result, $playerScore, $enemyScore]);

    $awarded = 0;

    if ($result === 'win') {
        $awarded = 100;
    }
    else if ($result === 'loss') {
        $awarded = -100;
    }

    $currentCredit = (int)$user['credit'];
        $newCredit = $currentCredit + $awarded;

        // ne menjen 0 alá
        if ($newCredit < 0) {
            $newCredit = 0;
        }

        $stmtUpdate = $pdo->prepare("
            UPDATE users
            SET credit = ?
            WHERE id = ?
        ");
    $stmtUpdate->execute([$newCredit, $userId]);

    $stmtNewCredit = $pdo->prepare("
        SELECT credit
        FROM users
        WHERE id = ?
        LIMIT 1
    ");
    $stmtNewCredit->execute([$userId]);
    $updatedUser = $stmtNewCredit->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'result' => $result,
        'awarded' => $awarded,
        'credits' => (int)$updatedUser['credit']
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}