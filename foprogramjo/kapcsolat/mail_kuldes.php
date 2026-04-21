<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../../vendor/autoload.php';

function sendKapcsolatEmail($nev, $email, $targy, $uzenet)
{
    if (empty($nev) || empty($email) || empty($uzenet)) {
        die("Minden mező kitöltése kötelező.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Érvénytelen email cím.");
    }


    $safeNev = htmlspecialchars($nev);
    $safeEmail = htmlspecialchars($email);
    $safeTargy = htmlspecialchars($targy);
    $safeUzenet = nl2br(htmlspecialchars($uzenet));

    $mailAdmin = new PHPMailer(true);
    $mailAdmin->isSMTP();
    $mailAdmin->Host       = 'smtp.gmail.com';
    $mailAdmin->SMTPAuth   = true;
    $mailAdmin->Username   = 'fizzpro2026@gmail.com';
    $mailAdmin->Password   = 'wflx zxjh vrxo pqjp';
    $mailAdmin->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mailAdmin->Port       = 587;
    $mailAdmin->CharSet    = 'UTF-8';

    $mailAdmin->setFrom('fizzpro2026@gmail.com', 'Fizz Liga Weboldal');
    $mailAdmin->addAddress('fizzpro2026@gmail.com', 'Fizz Liga');
    $mailAdmin->addReplyTo($email, $nev);

    $mailAdmin->isHTML(true);
    $mailAdmin->Subject = 'Kapcsolatfelvétel - ' . $targy;
    $mailAdmin->Body = "
        <h2>Új üzenet érkezett</h2>
        <p><strong>Név:</strong> {$safeNev}</p>
        <p><strong>Email:</strong> {$safeEmail}</p>
        <p><strong>Tárgy:</strong> {$safeTargy}</p>
        <p><strong>Üzenet:</strong></p>
        <p>{$safeUzenet}</p>
    ";
    $mailAdmin->AltBody =
        "Új üzenet érkezett\n\n" .
        "Név: {$nev}\n" .
        "Email: {$email}\n" .
        "Tárgy: {$targy}\n\n" .
        "Üzenet:\n{$uzenet}";

    $mailAdmin->send();

    $mailUser = new PHPMailer(true);
    $mailUser->isSMTP();
    $mailUser->Host       = 'smtp.gmail.com';
    $mailUser->SMTPAuth   = true;
    $mailUser->Username   = 'fizzpro2026@gmail.com';
    $mailUser->Password   = 'wflx zxjh vrxo pqjp';
    $mailUser->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mailUser->Port       = 587;
    $mailUser->CharSet    = 'UTF-8';

    $mailUser->setFrom('fizzpro2026@gmail.com', 'Fizz Liga');
    $mailUser->addAddress($email, $nev);

    $mailUser->isHTML(true);
    $mailUser->Subject = 'Köszönjük, hogy írtál nekünk!';
    $mailUser->Body = "
        <p>Szia {$safeNev}!</p>
        <p>Köszönjük, hogy kapcsolatba léptél velünk.</p>
        <p>Megkaptuk az üzeneted, és hamarosan megnézzük.</p>
        <p><strong>Tárgy:</strong> {$safeTargy}</p>
        <p><strong>A te üzeneted:</strong></p>
        <p>{$safeUzenet}</p>
        <br>
        <p>Üdvözlettel:<br>Fizz Liga csapata</p>
    ";
    $mailUser->AltBody =
        "Szia {$nev}!\n\n" .
        "Köszönjük, hogy kapcsolatba léptél velünk.\n" .
        "Megkaptuk az üzeneted, és hamarosan megnézzük.\n\n" .
        "Tárgy: {$targy}\n\n" .
        "A te üzeneted:\n{$uzenet}\n\n" .
        "Üdvözlettel:\nFizz Liga csapata";

    $mailUser->send();

    return true;
}