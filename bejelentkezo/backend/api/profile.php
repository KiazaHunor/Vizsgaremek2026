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
            'formatted_date' => date('Y. m. d. H:i', strtotime($user['created_at'])),
            'profile_image' => $user['profile_image']
        ]
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = trim($_POST['username'] ?? '');
    $newPassword = trim($_POST['password'] ?? '');

    $fields = [];
    $values = [];

    if ($newUsername !== '') {
        if (mb_strlen($newUsername) < 3) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'A felhasználónév legalább 3 karakter legyen'
            ]);
            exit();
        }

        if (mb_strlen($newUsername) > 50) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'A felhasználónév túl hosszú'
            ]);
            exit();
        }

        $checkUser = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $checkUser->execute([$newUsername, $currentUserId]);

        if ($checkUser->fetch()) {
            http_response_code(409);
            echo json_encode([
                'success' => false,
                'error' => 'Ez a felhasználónév már foglalt'
            ]);
            exit();
        }

        $fields[] = "username = ?";
        $values[] = $newUsername;
    }

    if ($newPassword !== '') {
        if (mb_strlen($newPassword) < 6) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'A jelszó legalább 6 karakter legyen'
            ]);
            exit();
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $fields[] = "password = ?";
        $values[] = $hashedPassword;
    }

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $fileName = $_FILES['profile_image']['name'];
        $fileTmp = $_FILES['profile_image']['tmp_name'];
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Csak JPG, JPEG, PNG vagy WEBP kép tölthető fel'
            ]);
            exit();
        }

        if ($_FILES['profile_image']['size'] > 2 * 1024 * 1024) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'A kép túl nagy, maximum 2 MB lehet'
            ]);
            exit();
        }

        $uploadDir = dirname(__DIR__) . '/uploads/profile_images/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $newFileName = 'user_' . $currentUserId . '_' . time() . '.' . $extension;
        $targetPath = $uploadDir . $newFileName;
        $dbPath = 'uploads/profile_images/' . $newFileName;

        if (!move_uploaded_file($fileTmp, $targetPath)) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Nem sikerült a képet feltölteni'
            ]);
            exit();
        }

        if (!empty($user['profile_image'])) {
            $oldPath = dirname(__DIR__) . '/' . $user['profile_image'];
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $fields[] = "profile_image = ?";
        $values[] = $dbPath;
    }

    if (empty($fields)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Nincs módosítandó adat'
        ]);
        exit();
    }

    $values[] = $currentUserId;

    $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
    $update = $pdo->prepare($sql);
    $update->execute($values);

    $stmt = $pdo->prepare("SELECT id, username, created_at, profile_image FROM users WHERE id = ?");
    $stmt->execute([$currentUserId]);
    $updatedUser = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'message' => 'A profil sikeresen frissítve lett.',
        'user' => [
            'id' => $updatedUser['id'],
            'username' => $updatedUser['username'],
            'created_at' => $updatedUser['created_at'],
            'formatted_date' => date('Y. m. d. H:i', strtotime($updatedUser['created_at'])),
            'profile_image' => $updatedUser['profile_image']
        ]
    ]);
    exit();
}

http_response_code(405);
echo json_encode([
    'success' => false,
    'error' => 'Nem támogatott kérés'
]);