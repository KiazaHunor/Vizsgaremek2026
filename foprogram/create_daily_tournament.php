<?php
require_once 'db.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    $today = date('Y-m-d');

    $name = 'Napi bajnokság - ' . $today;
    $startAt = $today . ' 19:35:00';
    $entryDeadline = $today . ' 19:40:00';
    $resultAt = $today . ' 19:45:00';

    $stmt = $pdo->prepare("
        SELECT id
        FROM tournaments
        WHERE DATE(start_at) = ?
        LIMIT 1
    ");
    $stmt->execute([$today]);

    if ($stmt->fetch()) {
        echo "Ma mar van bajnoksag.";
        exit;
    }

    $stmt = $pdo->prepare("
        INSERT INTO tournaments (name, start_at, entry_deadline, result_at, status)
        VALUES (?, ?, ?, ?, 'open')
    ");
    $stmt->execute([$name, $startAt, $entryDeadline, $resultAt]);

    echo "Bajnoksag letrehozva.";
} catch (Throwable $e) {
    echo "Hiba: " . $e->getMessage();
}