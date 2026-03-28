<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nev = trim($_POST["nev"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $uzenet = trim($_POST["uzenet"] ?? "");

    if (empty($nev) || empty($email) || empty($uzenet)) {
        die("Minden mező kitöltése kötelező.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Érvénytelen email cím.");
    }

    $cimzett = "fizzliga@gmail.com";
    $targy = "Új kapcsolatfelvételi üzenet a weboldalról";

    $tartalom = "Név: $nev\n";
    $tartalom .= "Email: $email\n\n";
    $tartalom .= "Üzenet:\n$uzenet\n";

    $fejlec = "From: noreply@fizzliga.hu\r\n";
    $fejlec .= "Reply-To: $email\r\n";
    $fejlec .= "Content-Type: text/plain; charset=UTF-8\r\n";

    if (mail($cimzett, $targy, $tartalom, $fejlec)) {
        echo "Az üzenet sikeresen elküldve!";
    } else {
        echo "Hiba történt az üzenet küldése közben.";
    }
} else {
    echo "Érvénytelen kérés.";
}
?>