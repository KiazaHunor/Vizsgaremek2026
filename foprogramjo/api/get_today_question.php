<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../db.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM questions WHERE active_date = CURDATE() LIMIT 1");
    $stmt->execute();
    $question = $stmt->fetch();

    if (!$question) {
        echo json_encode([
            "success" => false,
            "error" => "Ma nincs aktív kérdés."
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode([
        "success" => true,
        "question" => [
            "id" => (int)$question["id"],
            "text" => $question["text"],
            "answerA" => $question["answer_a"],
            "answerB" => $question["answer_b"],
            "answerC" => $question["answer_c"],
            "answerD" => $question["answer_d"],
            "correctAnswer" => $question["correct_answer"],
            "activeDate" => $question["active_date"]
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Szerverhiba: " . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}