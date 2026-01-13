<?php
// Adatbázis kapcsolat
$servername = "localhost";
$username = "root";
$password = "";  // Alapértelmezett XAMPP jelszó (üres)
$dbname = "user_auth";

// Kapcsolódás létrehozása
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Kapcsolat ellenőrzése
if (!$conn) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Adatbázis kapcsolódási hiba: ' . mysqli_connect_error()
    ]);
    exit();
}

// Karakterkészlet beállítása
mysqli_set_charset($conn, "utf8mb4");

// Időzóna beállítása
date_default_timezone_set('Europe/Budapest');