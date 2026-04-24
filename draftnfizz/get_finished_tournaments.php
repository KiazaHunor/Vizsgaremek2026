<?php
require_once 'db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = $pdo->query("
        SELECT id, name, start_at, entry_deadline, result_at
        FROM tournaments
        WHERE status = 'finished'
        ORDER BY result_at DESC
        LIMIT 8
    ");

    echo json_encode([
        "success" => true,
        "tournaments" => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}