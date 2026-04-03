<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/mail_kuldes.php';
require_once __DIR__ . '/../../bejelentkezo/backend/api/auth.php';
require_once __DIR__ . '/../../bejelentkezo/backend/db.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "error" => "Csak POST kérés engedélyezett."
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "error" => "Hibás JSON"
    ]);
    exit;
}

$targy = trim($data["targy"] ?? "");
$uzenet = trim($data["uzenet"] ?? "");

if ($targy === "" || $uzenet === "") {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "error" => "Hiányzó adatok"
    ]);
    exit;
}

if (($GLOBALS['current_user_id'] ?? 0) <= 0) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "error" => "Nem vagy bejelentkezve."
    ]);
    exit;
}

$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ? LIMIT 1");
$stmt->execute([$GLOBALS['current_user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    http_response_code(404);
    echo json_encode([
        "success" => false,
        "error" => "Felhasználó nem található."
    ]);
    exit;
}

$nev = trim($user["username"] ?? "");
$email = trim($user["email"] ?? "");

try {
    sendKapcsolatEmail($nev, $email, $targy, $uzenet);

    echo json_encode([
        "success" => true,
        "message" => "Az üzenet sikeresen elküldve."
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}