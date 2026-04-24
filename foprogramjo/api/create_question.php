<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

$text = trim($data['text'] ?? '');
$answerA = trim($data['answer_a'] ?? '');
$answerB = trim($data['answer_b'] ?? '');
$answerC = trim($data['answer_c'] ?? '');
$answerD = trim($data['answer_d'] ?? '');
$correctAnswer = trim($data['correct_answer'] ?? '');

if (
    $text === '' || $answerA === '' || $answerB === '' ||
    $answerC === '' || $answerD === '' || $correctAnswer === ''
) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "error" => "Minden mező kitöltése kötelező."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO questions (text, answer_a, answer_b, answer_c, answer_d, correct_answer)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$text, $answerA, $answerB, $answerC, $answerD, $correctAnswer]);

    echo json_encode([
        "success" => true
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Szerverhiba: " . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}