<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';

// Csak POST kérések fogadása
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Csak POST kérés engedélyezett']);
    exit();
}

// JSON adatok beolvasása
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Adatok ellenőrzése
if (!$data || !isset($data['username']) || !isset($data['password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Hiányzó felhasználónév vagy jelszó']);
    exit();
}

// Adatok tisztítása
$username = mysqli_real_escape_string($conn, trim($data['username']));
$password = trim($data['password']);

// Validáció
if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Felhasználónév és jelszó megadása kötelező']);
    exit();
}

if (strlen($username) < 3) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'A felhasználónév legalább 3 karakter hosszú legyen']);
    exit();
}

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'A jelszó legalább 6 karakter hosszú legyen']);
    exit();
}

// Felhasználónév egyediség ellenőrzése
$check_sql = "SELECT id FROM users WHERE username = '$username'";
$check_result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_result) > 0) {
    http_response_code(409);
    echo json_encode(['success' => false, 'error' => 'A felhasználónév már foglalt']);
    exit();
}

// Jelszó hashelése
$hashed_password = hash('sha256', $password);

// Felhasználó beszúrása
$insert_sql = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";
$insert_result = mysqli_query($conn, $insert_sql);

if (!$insert_result) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Regisztráció sikertelen: ' . mysqli_error($conn)]);
    exit();
}

// Sikeres válasz
echo json_encode([
    'success' => true,
    'message' => 'Sikeres regisztráció! Most már bejelentkezhet.',
    'username' => $username
]);