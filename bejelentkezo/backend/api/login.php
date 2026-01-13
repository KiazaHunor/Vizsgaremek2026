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
$password = hash('sha256', trim($data['password']));

// Felhasználó keresése
$sql = "SELECT id, username, password FROM users WHERE username = '$username'";
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

// Jelszó ellenőrzése
if ($user['password'] !== $password) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Hibás felhasználónév vagy jelszó']);
    exit();
}

// Token generálása
$token = bin2hex(random_bytes(32));
$expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

// Token frissítése az adatbázisban
$update_sql = "UPDATE users SET token = '$token', token_expiry = '$expiry' WHERE id = " . $user['id'];
if (!mysqli_query($conn, $update_sql)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Token mentési hiba']);
    exit();
}

// Sikeres válasz
echo json_encode([
    'success' => true,
    'token' => $token,
    'expires' => $expiry,
    'username' => $user['username'],
    'message' => 'Sikeres bejelentkezés'
]);