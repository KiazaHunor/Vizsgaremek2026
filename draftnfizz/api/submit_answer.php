<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

$userId = (int)($data['user_id'] ?? 0);
$questionId = (int)($data['question_id'] ?? 0);
$selectedAnswer = trim($data['selected_answer'] ?? '');

if ($userId <= 0 || $questionId <= 0 || $selectedAnswer === '') {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "error" => "Hiányzó vagy hibás adatok."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        SELECT * FROM user_answers
        WHERE user_id = ? AND question_id = ? AND answer_date = CURDATE()
        LIMIT 1
    ");
    $stmt->execute([$userId, $questionId]);
    $existing = $stmt->fetch();

    if ($existing) {
        $pdo->rollBack();
        echo json_encode([
            "success" => false,
            "already_answered" => true,
            "error" => "Ma már válaszoltál erre a kérdésre."
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM questions WHERE id = ? LIMIT 1");
    $stmt->execute([$questionId]);
    $question = $stmt->fetch();

    if (!$question) {
        $pdo->rollBack();
        echo json_encode([
            "success" => false,
            "error" => "A kérdés nem található."
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $isCorrect = ($selectedAnswer === $question['correct_answer']) ? 1 : 0;

    $stmt = $pdo->prepare("
        INSERT INTO user_answers (user_id, question_id, selected_answer, answer_date, is_correct)
        VALUES (?, ?, ?, CURDATE(), ?)
    ");
    $stmt->execute([$userId, $questionId, $selectedAnswer, $isCorrect]);

    $stmt = $pdo->prepare("SELECT current_streak, best_streak FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user) {
        $pdo->rollBack();
        echo json_encode([
            "success" => false,
            "error" => "A felhasználó nem található."
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $currentStreak = (int)$user['current_streak'];
    $bestStreak = (int)$user['best_streak'];

    if ($isCorrect) {
        $currentStreak++;
        if ($currentStreak > $bestStreak) {
            $bestStreak = $currentStreak;
        }
    } else {
        $currentStreak = 0;
    }

    $stmt = $pdo->prepare("
        UPDATE users
        SET current_streak = ?, best_streak = ?
        WHERE id = ?
    ");
    $stmt->execute([$currentStreak, $bestStreak, $userId]);

    $pdo->commit();

    echo json_encode([
        "success" => true,
        "is_correct" => (bool)$isCorrect,
        "correct_answer" => $question['correct_answer'],
        "current_streak" => $currentStreak,
        "best_streak" => $bestStreak,
        "already_answered" => false
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