<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';

header('Content-Type: text/html; charset=utf-8');

//var_dump($_GET);
if (!isset($_GET['token']) || empty($_GET['token'])) {
    die("Érvénytelen email token.");
}

$email_token = $_GET['token']; 
try {
    $stmt = $pdo->prepare("SELECT id, username, email_verified FROM users WHERE email_token = ?");
    $stmt->execute([$email_token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Érvénytelen email token.");
    }

    if ($user['email_verified'] == 1) {
        $message = "Az email már megerősítve volt.";
    } else {
        $updateStmt = $pdo->prepare("UPDATE users SET email_verified = 1, email_token = NULL WHERE id = ?");
        $updateStmt->execute([$user['id']]);
        $message = "Sikeres email megerősítés!";
    }

    $username = htmlspecialchars($user['username']);
} catch (Exception $e) {
    die("Hiba: " . $e->getMessage());
}


?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Email megerősítés</title>
    <link rel="stylesheet" href="../verify.css">
</head>
<body>
    <div class="card">
        <div class="logo">⚡</div>
        <h1>Az email címedet sikeresen megerősítetted</h1>
        <p>Kedves <?php echo $username; ?>, az oldal <span class="countdown">10</span> másodperc múlva kikapcsol.</p>
        <footer>
            &copy; 2026 YourSite • <a href="#">Privacy</a> • <a href="#">Contact</a>
        </footer>
    </div>

    <script>
        let countdown = 10;
        const countdownEl = document.querySelector('.countdown');

        const interval = setInterval(() => {
            countdown--;
            countdownEl.textContent = countdown;
            if (countdown <= 0) {
                clearInterval(interval);
                window.location.href = '../login.php';
            }
        }, 1000);
    </script>
</body>
</html>