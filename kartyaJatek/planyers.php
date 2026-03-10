<?php
// 1. Adatbázis kapcsolat
$host = 'localhost';
$db   = 'fizzliga_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// 2. Játékosok lekérése az összes szükséges adattal
$sql = "
SELECT 
    p.id,
    p.name,
    ps.attack,
    ps.controll,
    ps.defence,
    t.name AS team,
    pos.name AS position,
    tk.image_path AS shirt_image
FROM players p
JOIN teams t ON p.team_id = t.id
JOIN positions pos ON p.position_id = pos.id
LEFT JOIN player_stats ps ON ps.player_id = p.id
LEFT JOIN team_kits tk ON tk.team_id = t.id
ORDER BY p.id
";

$stmt = $pdo->query($sql);
$players = $stmt->fetchAll();

// 3. JSON a JavaScriptnek
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
        <div id="player-battle" class="battle-card"></div>
        <div id="enemy-battle" class="battle-card"></div>
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

<div class="card fut-card">
    <div class="card-glow"></div>

    <div class="card-top">
        <div class="card-position">LW</div>
    </div>
    <div class="card-image">
        <img src="hatternelkul/barcika.png" alt="Mez">
    </div>

    <div class="card-name">Varga Barnabás</div>
    <div class="card-team">Ferencváros</div>

    <div class="card-stats">
        <div class="stat-box">
            <span>ATK</span>
            <span>95</span>
        </div>
        <div class="stat-box">
            <span>CTR</span>
            <span>82</span>
        </div>
        <div class="stat-box">
            <span>DEF</span>
            <span>40</span>
        </div>
    </div>
</div>

<script>
    const players = <?php echo $playersJson; ?>;
</script>

<div id="game-message" class="game-message"></div>
<script src="script.js"></script>
</body>
</html>
