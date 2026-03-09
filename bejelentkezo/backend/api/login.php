<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Csak POST kérés engedélyezett']);
    exit();
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['username']) || !isset($data['password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Hiányzó felhasználónév vagy jelszó']);
    exit();
}

$username = mysqli_real_escape_string($conn, trim($data['username']));
$password = trim($data['password']);

$sql = "SELECT id, username, password, email_verified FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Adatbázis hiba: ' . mysqli_error($conn)]);
    exit();
}

if (mysqli_num_rows($result) !== 1) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Hibás felhasználónév vagy jelszó']);
    exit();
}

$user = mysqli_fetch_assoc($result);

if (!password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Hibás felhasználónév vagy jelszó']);
    exit();
}

if ($user["email_verified"] == 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Nem erősítetted meg az email címedet!']);
    exit();
}

$token = bin2hex(random_bytes(32));
$expiry = date('Y-m-d H:i:s', strtotime('+12 hour'));

$update_sql = "UPDATE users SET token = '$token', token_expiry = '$expiry' WHERE id = " . $user['id'];
if (!mysqli_query($conn, $update_sql)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Token mentési hiba']);
    exit();
}

echo json_encode([
    'success' => true,
    'token' => $token,
    'expires' => $expiry,
    'username' => $user['username'],
    'message' => 'Sikeres bejelentkezés'
]);