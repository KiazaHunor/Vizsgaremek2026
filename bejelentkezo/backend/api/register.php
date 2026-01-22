<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Csak POST kérés engedélyezett']);
    exit();
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['username']) || !isset($data['password']) || !isset($data['password_conf'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Hiányzó adatok']);
    exit();
}

$username = mysqli_real_escape_string($conn, trim($data['username']));
$password = trim($data['password']);
$password_conf = trim($data['password_conf']);

if (empty($username) || empty($password) || empty($password_conf)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Minden mező kötelező']);
    exit();
}

if (strlen($username) < 3) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'A felhasználónév legalább 3 karakter']);
    exit();
}

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'A jelszó legalább 6 karakter']);
    exit();
}

if ($password !== $password_conf) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'A jelszavak nem egyeznek']);
    exit();
}

$check = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
if (mysqli_num_rows($check) > 0) {
    http_response_code(409);
    echo json_encode(['success' => false, 'error' => 'A felhasználónév már foglalt']);
    exit();
}

$hashed_password = hash('sha256', $password);

$sql = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";
if (!mysqli_query($conn, $sql)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Regisztráció sikertelen']);
    exit();
}

echo json_encode([
    'success' => true,
    'message' => 'Sikeres regisztráció'
]);
