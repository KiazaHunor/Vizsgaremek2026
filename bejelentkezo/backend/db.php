<?php
// Adatbázis kapcsolat
$host = "localhost";
$dbname = "user_auth";
$dbuser = "root";
$dbpass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $conn = mysqli_connect($host, $dbuser, $dbpass, $dbname);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Adatbázis kapcsolódási hiba: ' . $e->getMessage()
    ]);

}
