<?php
require_once 'db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = $pdo->query("
        SELECT id, name, start_at, entry_deadline, result_at, status
        FROM tournaments
        WHERE status = 'open'
          AND NOW() BETWEEN start_at AND entry_deadline
        ORDER BY start_at DESC
        LIMIT 1
    ");

    $tournament = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'tournament' => $tournament ?: null
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}