<?php
require_once 'auth.php';

// Csak GET kérések fogadása
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Csak GET kérés engedélyezett']);
    exit();
}

// Felhasználó adatainak visszaadása
$user = $GLOBALS['current_user'];

echo json_encode([
    'success' => true,
    'user' => [
        'id' => $user['id'],
        'username' => $user['username'],
        'created_at' => $user['created_at'],
        'formatted_date' => date('Y. m. d. H:i', strtotime($user['created_at']))
    ],
    'message' => 'Sikeres tokenes elérés',
    'timestamp' => date('Y-m-d H:i:s')
]);