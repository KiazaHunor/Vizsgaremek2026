<?php
require_once 'auth.php';

header('Content-Type: application/json; charset=utf-8');

$user = $GLOBALS['current_user'];
$currentUserId = (int)$GLOBALS['current_user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'created_at' => $user['created_at'],
            'formatted_date' => date('Y. m. d. H:i', strtotime($user['created_at']))
        ]
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $newUsername = trim($data['username'] ?? '');

    if ($newUsername === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'A felhasználónév nem lehet üres']);
        exit();
    }

    if (mb_strlen($newUsername) < 3) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'A felhasználónév legalább 3 karakter legyen']);
        exit();
    }

    if (mb_strlen($newUsername) > 50) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'A felhasználónév túl hosszú']);
        exit();
    }

    $check = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $check->execute([$newUsername, $currentUserId]);

    if ($check->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'error' => 'Ez a felhasználónév már foglalt']);
        exit();
    }

    $update = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
    $update->execute([$newUsername, $currentUserId]);

    echo json_encode([
        'success' => true,
        'message' => 'A profil sikeresen frissítve lett.',
        'user' => [
            'id' => $currentUserId,
            'username' => $newUsername
        ]
    ]);
    exit();
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Nem támogatott kérés']);