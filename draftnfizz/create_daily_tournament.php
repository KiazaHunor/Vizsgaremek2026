<?php
require_once 'db.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    $today = date('Y-m-d');

    // 4 időszak
    $tournaments = [
        ['08:00:00', '10:00:00'],
        ['12:00:00', '14:00:00'],
        ['16:00:00', '18:00:00'],
        ['20:00:00', '22:00:00'],
    ];

    foreach ($tournaments as $index => $t) {
        $startAt = $today . ' ' . $t[0];
        $entryDeadline = $today . ' ' . $t[1];
        $resultAt = date('Y-m-d H:i:s', strtotime($entryDeadline . ' +10 seconds'));

        $name = 'Napi bajnokság #' . ($index + 1) . ' - ' . $today;

        // CHECK: már létezik ilyen idősávval?
        $stmt = $pdo->prepare("
            SELECT id
            FROM tournaments
            WHERE start_at = ? AND entry_deadline = ?
            LIMIT 1
        ");
        $stmt->execute([$startAt, $entryDeadline]);

        if ($stmt->fetch()) {
            echo "Mar letezik: " . $name . "\n";
            continue;
        }

        // INSERT
        $stmt = $pdo->prepare("
            INSERT INTO tournaments (name, start_at, entry_deadline, result_at, status)
            VALUES (?, ?, ?, ?, 'open')
        ");
        $stmt->execute([$name, $startAt, $entryDeadline, $resultAt]);

        echo "Letrehozva: " . $name . "\n";
    }

} catch (Throwable $e) {
    echo "Hiba: " . $e->getMessage();
}