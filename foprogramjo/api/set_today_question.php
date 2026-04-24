<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../db.php';

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("UPDATE questions SET active_date = NULL WHERE active_date = CURDATE()");
    $stmt->execute();

    $stmt = $pdo->query("SELECT id FROM questions ORDER BY id DESC LIMIT 1");
    $latest = $stmt->fetch();

    if (!$latest) {
        $pdo->rollBack();
        echo json_encode([
            "success" => false,
            "error" => "Nincs még elmentett kérdés."
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE questions SET active_date = CURDATE() WHERE id = ?");
    $stmt->execute([(int)$latest['id']]);

    $pdo->commit();

    echo json_encode([
        "success" => true
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Szerverhiba: " . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}