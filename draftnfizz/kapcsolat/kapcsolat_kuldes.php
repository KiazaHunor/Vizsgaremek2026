<?php
require_once __DIR__ . '/mail_kuldes.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Érvénytelen kérés.");
}

$nev = trim($_POST["kapcsolatnev"] ?? "");
$email = trim($_POST["kapcsolatemail"] ?? "");
$targy = trim($_POST["targy"] ?? "Kapcsolatfelvétel");
$uzenet = trim($_POST["uzenet"] ?? "");

try {
    sendKapcsolatEmail($nev, $email, $targy, $uzenet);

    echo "
    <!DOCTYPE html>
    <html lang='hu'>
    <head>
        <meta charset='UTF-8'>
        <title>Sikeres küldés</title>
    </head>
    <body>
        <h2>Az üzenet sikeresen elküldve!</h2>
        <p>Visszaigazoló emailt is küldtünk a megadott címre.</p>
        <a href='kapcsolat.html'>Vissza a kapcsolat oldalra</a>
    </body>
    </html>
    ";
} catch (Exception $e) {
    error_log("Kapcsolat email küldési hiba: " . $e->getMessage());
    echo "Hiba történt az email küldése közben: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}