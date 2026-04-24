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

    $stmt = $pdo->prepare("
        SELECT credit
        FROM users
        WHERE token = ?
          AND token_expiry IS NOT NULL
          AND token_expiry > NOW()
        LIMIT 1
    ");
    $stmt->execute([$token]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('Érvénytelen token.');
    }

    echo json_encode([
        'success' => true,
        'credits' => (int)$user['credit']
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}