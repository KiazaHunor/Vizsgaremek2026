<?php
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
$playersJson = json_encode($players);
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fizz Liga - Kártyajáték</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="game-area">
    <a href="../foprogram/szep.html" class="btn-back">⬅ Vissza a főoldalra</a>

    <div class="scoreboard">
        <div class="score-box">
            <span class="score-label">Játékos</span>
            <span id="player-score" class="score-value">0</span>
        </div>
        <div class="score-divider">:</div>
        <div class="score-box">
            <span class="score-label">Ellenfél</span>
            <span id="enemy-score" class="score-value">0</span>
        </div>
    </div>

    <div class="deck enemy-deck">Ellenfél pakli</div>

    <div class="hand enemy-hand" id="enemy-hand"></div>

    <div class="battle-area">
        <div id="player-battle" class="battle-card"></div>
        <div id="enemy-battle" class="battle-card"></div>
    </div>

    <div class="center-controls">
        <button id="play-round">Kör lejátszása</button>
    </div>

    <div class="hand player-hand" id="player-hand"></div>

    <div class="deck player-deck" id="player-deck">Játékos pakli</div>
</div>

<div id="game-message" class="game-message"></div>

<script>
    const players = <?php echo $playersJson; ?>;
</script>
<script src="script.js"></script>
</body>
</html>