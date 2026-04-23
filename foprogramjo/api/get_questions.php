<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../db.php';

try {
    $stmt = $pdo->query("SELECT * FROM questions");
    $questions = $stmt->fetchAll();

    echo json_encode([
        "success" => true,
        "questions" => $questions
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}