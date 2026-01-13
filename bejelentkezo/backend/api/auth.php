<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';

// Token kinyerése a fejlécből
$headers = getallheaders();
$auth_header = '';

// Különböző fejlécnév lehetőségek
if (isset($headers['Authorization'])) {
    $auth_header = $headers['Authorization'];
} elseif (isset($headers['authorization'])) {
    $auth_header = $headers['authorization'];
} elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $auth_header = $_SERVER['HTTP_AUTHORIZATION'];
}

// Token ellenőrzése
if (empty($auth_header) || !str_starts_with($auth_header, 'Bearer ')) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Hiányzó vagy érvénytelen token']);
    exit();
}

// Token kinyerése
$token = mysqli_real_escape_string($conn, substr($auth_header, 7));

// Token érvényességének ellenőrzése
$sql = "SELECT id, username, created_at FROM users WHERE token = '$token' AND token_expiry > NOW()";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) !== 1) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Érvénytelen vagy lejárt token']);
    exit();
}

// Felhasználó adatok
$user = mysqli_fetch_assoc($result);

// Felhasználó azonosítója a munkamenethez
$GLOBALS['current_user_id'] = $user['id'];
$GLOBALS['current_user'] = $user;