<?php
// 1. Adatbázis kapcsolat
$host = 'localhost';
$db   = 'fizzliga_dbproba';
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
    <style>
        body { margin: 0; padding: 0; }
        .deck { border: 1px solid black; width: 120px; height: 180px; position: fixed; top: 50%; transform: translateY(-50%); text-align: center; line-height: 180px; cursor: pointer; }
        #player-deck { left: 20px; }
        #enemy-deck { right: 20px; cursor: default; }
        .table { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 80%; text-align: center; }
        .hand { display: flex; justify-content: center; margin: 20px 0; }
        .card { border: 1px solid black; padding: 10px; margin: 5px; width: 120px; height: 180px; text-align: center; line-height: 1.2; font-size: 14px; display: flex; flex-direction: column; justify-content: center; }
    </style>
</head>
<body>

<!-- Ellenfél pakli -->
<div id="enemy-deck" class="deck">Ellenfél pakli</div>

<!-- Játékos pakli -->
<div id="player-deck" class="deck">Játékos pakli</div>

<!-- Középső terület -->
<div class="table">
    <div id="enemy-hand" class="hand"></div>
    <hr>
    <div id="player-hand" class="hand"></div>
</div>

<script>
    const playerDeck = document.getElementById("player-deck");
    const playerHand = document.getElementById("player-hand");
    const enemyHand = document.getElementById("enemy-hand");

    // Adatbázisból jövő játékosok
    const players = <?php echo $playersJson; ?>;

    playerDeck.addEventListener("click", dealCards);

    function dealCards() {
    playerHand.innerHTML = "";
    enemyHand.innerHTML = "";

    for (let i = 0; i < 5; i++) {
        const playerData = players[Math.floor(Math.random() * players.length)];
        const enemyData  = players[Math.floor(Math.random() * players.length)];

        const playerCard = document.createElement("div");
        playerCard.className = "card";
        playerCard.innerHTML = `
            <strong>${playerData.name}</strong><br>
            Team: ${playerData.team}<br>
            Position: ${playerData.position}<br>
            Attack: ${playerData.attack}<br>
            Controll: ${playerData.controll}<br>
            Defence: ${playerData.defence}
        `;
        playerHand.appendChild(playerCard);

        const enemyCard = document.createElement("div");
        enemyCard.className = "card";
        enemyCard.innerHTML = `
            <strong>${enemyData.name}</strong><br>
            Team: ${enemyData.team}<br>
            Position: ${enemyData.position}<br>
            Attack: ${enemyData.attack}<br>
            Controll: ${enemyData.controll}<br>
            Defence: ${enemyData.defence}
        `;
        enemyHand.appendChild(enemyCard);
    }
}

   
</script>

</body>
</html>
