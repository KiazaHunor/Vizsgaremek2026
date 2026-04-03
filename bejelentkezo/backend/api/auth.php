<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';

$headers = getallheaders();
$authHeader = '';

if (isset($headers['Authorization'])) {
    $authHeader = $headers['Authorization'];
} elseif (isset($headers['authorization'])) {
    $authHeader = $headers['authorization'];
} elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
}

if ($authHeader === '' || strpos($authHeader, 'Bearer ') !== 0) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Hiányzó vagy érvénytelen token'
    ]);
    exit();
}

$token = trim(substr($authHeader, 7));

$stmt = $pdo->prepare("
    SELECT id, username, email, created_at, profile_image 
    FROM users 
    WHERE token = ? AND token_expiry > NOW() 
    LIMIT 1
");
$stmt->execute([$token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Érvénytelen vagy lejárt token'
    ]);
    exit();
}

$GLOBALS['current_user_id'] = (int)$user['id'];
$GLOBALS['current_user'] = $user;