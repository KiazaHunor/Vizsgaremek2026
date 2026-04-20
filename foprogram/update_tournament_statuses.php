<?php
require_once 'db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $changes = [];

    // OPEN -> CLOSED
    $stmt = $pdo->prepare("
        UPDATE tournaments
        SET status = 'closed'
        WHERE status = 'open'
          AND NOW() > entry_deadline
    ");
    $stmt->execute();
    $closedCount = $stmt->rowCount();

    $changes[] = [
        'from' => 'open',
        'to' => 'closed',
        'count' => $closedCount
    ];

    echo json_encode([
        'success' => true,
        'message' => 'Státuszok frissítve.',
        'changes' => $changes
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}