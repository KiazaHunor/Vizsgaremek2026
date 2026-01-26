<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require __DIR__ . '/../../../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Csak POST kérés engedélyezett']);
    exit();
}

// JSON input feldolgozása
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['username'], $data['password'], $data['password_conf'], $data['email'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Hiányzó adatok (felhasználónév, jelszó, email)']);
    exit();
}

$username = trim($data['username']);
$email = trim($data['email']);
$password = trim($data['password']);
$password_conf = trim($data['password_conf']);

// Validálás
if (empty($username) || empty($password) || empty($password_conf) || empty($email)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Minden mező kitöltése kötelező']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Érvénytelen email cím formátum']);
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

// Ellenőrzés, hogy létezik-e már username vagy email
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$stmt->execute([$username, $email]);
if ($stmt->rowCount() > 0) {
    http_response_code(409);
    echo json_encode(['success' => false, 'error' => 'A felhasználónév vagy email már foglalt']);
    exit();
}

// Hash a jelszóhoz
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Email token generálása
$email_token = bin2hex(random_bytes(32));

try {
    // Beszúrás az adatbázisba
    $stmt = $pdo->prepare("INSERT INTO users (username, password, email, email_token, email_verified) VALUES (?, ?, ?, ?, 0)");
    $stmt->execute([$username, $hashed_password, $email, $email_token]);

    // PHPMailer
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'probaa288@gmail.com';
    $mail->Password   = 'lbgv ifzc jxyc lize'; 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom('probaa288@gmail.com', 'Weboldal');
    $mail->addAddress($email, $username);
    $mail->isHTML(true);
    
    $verify_link = "http://localhost/verify_email.php?token=$email_token";
    $mail->Subject = "Email megerősítés";
    $mail->Body    = "Szia $username!<br><br>Kattints a linkre a fiókod aktiválásához:<br>
                      <a href='$verify_link'>$verify_link</a><br><br>Köszönjük!";
    
    $mail->send();
    $email_sent = true;
    
} catch (Exception $e) {
    error_log("Email küldési hiba: " . $e->getMessage());
    $email_sent = false;
}

// Válasz a kliensnek
$response = [
    'success' => true,
    'email_sent' => $email_sent,
    'message' => $email_sent ? 'Sikeres regisztráció. Emailt küldtünk a megadott címre.' : 'Sikeres regisztráció, de az email nem lett elküldve.'
];

echo json_encode($response);
