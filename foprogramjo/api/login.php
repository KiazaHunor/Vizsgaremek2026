<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';

if ($username === '' || $password === '') {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "error" => "Hiányzó felhasználónév vagy jelszó."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode([
            "success" => false,
            "error" => "Hibás felhasználónév vagy jelszó."
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!password_verify($password, $user['password'])) {
        echo json_encode([
            "success" => false,
            "error" => "Hibás felhasználónév vagy jelszó."
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode([
        "success" => true,
        "user" => [
            "id" => (int)$user["id"],
            "username" => $user["username"],
            "password" => "",
            "email" => $user["email"],
            "token" => $user["token"],
            "tokenExpiry" => $user["token_expiry"],
            "passwordResetToken" => $user["password_reset_token"],
            "passwordResetExpiry" => $user["password_reset_expiry"],
            "createdAt" => $user["created_at"],
            "emailToken" => $user["email_token"],
            "emailVerified" => (bool)$user["email_verified"],
            "profileImage" => $user["profile_image"],
            "currentStreak" => (int)$user["current_streak"],
            "bestStreak" => (int)$user["best_streak"]
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Szerverhiba: " . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}