<?php
// 1. Adatbázis kapcsolat
$host = 'localhost';
$db   = 'fizzliga_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// 2. Lekérjük a játékosokat
$sql = "SELECT * FROM players ORDER BY id";
$stmt = $pdo->query($sql);
$players = $stmt->fetchAll();

// 3. JSON-ba konvertáljuk a JavaScripthez
$playersJson = json_encode($players);
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Kártyajáték</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="game-area">
    <div class="scoreboard">
        <div>Játékos: <span id="player-score">0</span></div>
        <div>Ellenfél: <span id="enemy-score">0</span></div>
    </div>

    <!-- Ellenfél pakli -->
    <div class="deck enemy-deck">Ellenfél Pakli</div>

    <!-- Ellenfél lapjai -->
    <div class="hand enemy-hand" id="enemy-hand"></div>

    <div class="battle-area">
        <div id="player-battle"></div>
        <div id="enemy-battle"></div>
    </div>

    <!-- Játékos lapjai -->
    <div class="hand player-hand" id="player-hand"></div>
    <div class="stat-buttons">
        <button data-stat="attack">Attack</button>
        <button data-stat="controll">Controll</button>
        <button data-stat="defence">Defence</button>        
    </div>
    <button id="play-round">Kör lejátszása</button>



    <!-- Játékos pakli -->
    <div class="deck player-deck" id="player-deck">Játékos Pakli</div>

</div>

<script>
    const players = <?php echo $playersJson; ?>;
</script>


<script src="script.js"></script>
</body>
</html>
