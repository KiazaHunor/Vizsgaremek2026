<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require __DIR__ . '/../../../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Csak POST kérés engedélyezett']);
    exit();
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['action'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Hiányzó művelet']);
    exit();
}

$action = $data['action'];

if ($action === 'request') {
    $email = trim($data['email'] ?? '');

    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Érvénytelen email cím']);
        exit();
    }

    $stmt = $pdo->prepare('SELECT id, username, email_verified FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $genericMessage = 'Ha az email cím szerepel a rendszerben, elküldtük a jelszó-visszaállító linket.';

    if (!$user) {
        echo json_encode(['success' => true, 'message' => $genericMessage]);
        exit();
    }

    if ((int)$user['email_verified'] !== 1) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Az email cím még nincs megerősítve.']);
        exit();
    }

    $resetToken = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

    $update = $pdo->prepare('UPDATE users SET password_reset_token = ?, password_reset_expiry = ? WHERE id = ?');
    $update->execute([$resetToken, $expiry, $user['id']]);

    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'probaa288@gmail.com';
        $mail->Password   ='gsru elku prue lbrl'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom('probaa288@gmail.com', 'Weboldal');
        $mail->addAddress($email, $user['username']);
        $mail->isHTML(true);

        $resetLink = 'http://localhost/oliverhtdoc/Vizsgaremek2026/bejelentkezo/frontend/reset_jelszo.html?token=' . urlencode($resetToken);
        $mail->Subject = 'Jelszó visszaállítás';
        $mail->Body = "Szia {$user['username']}!<br><br>Kattints az alábbi linkre az új jelszó beállításához:<br><a href='$resetLink'>$resetLink</a><br><br>A link 1 óráig érvényes.";

        $mail->send();

        echo json_encode(['success' => true, 'message' => $genericMessage]);
        exit();
    } catch (Exception $e) {
        error_log('Jelszó-visszaállító email küldési hiba: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Az email küldése nem sikerült']);
        exit();
    }
}

if ($action === 'reset') {
    $token = trim($data['token'] ?? '');
    $password = trim($data['password'] ?? '');
    $password_conf = trim($data['password_conf'] ?? '');

    if (!$token || !$password || !$password_conf) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Hiányzó adatok']);
        exit();
    }

    if (strlen($password) < 6) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'A jelszó legalább 6 karakter legyen']);
        exit();
    }

    if ($password !== $password_conf) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'A jelszavak nem egyeznek']);
        exit();
    }

    $stmt = $pdo->prepare('SELECT id FROM users WHERE password_reset_token = ? AND password_reset_expiry > NOW() LIMIT 1');
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Érvénytelen vagy lejárt token']);
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $update = $pdo->prepare('UPDATE users SET password = ?, password_reset_token = NULL, password_reset_expiry = NULL WHERE id = ?');
    $update->execute([$hashedPassword, $user['id']]);

    echo json_encode(['success' => true, 'message' => 'A jelszó sikeresen frissítve lett.']);
    exit();
}

http_response_code(400);
echo json_encode(['success' => false, 'error' => 'Ismeretlen művelet']);
