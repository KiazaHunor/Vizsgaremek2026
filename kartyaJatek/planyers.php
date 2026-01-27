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
                body {
                    margin: 0;
                    padding: 0;
                    background: #f5f5f5;
                    font-family: Arial, sans-serif;
                }

                .game-area {
                    position: relative;
                    width: 100vw;
                    height: 100vh;
                }

                /* Paklik */
                .deck {
                    width: 140px;
                    height: 200px;
                    border: 3px solid black;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: bold;
                    background: white;
                }

                .player-deck {
                    position: absolute;
                    left: 40px;
                    bottom: 40px;
                    cursor: pointer;
                }

                .enemy-deck {
                    position: absolute;
                    right: 40px;
                    top: 40px;
                }

                /* Kezek */
                .hand {
                    display: flex;
                    gap: 15px;
                    justify-content: center;
                }

                .enemy-hand {
                    position: absolute;
                    top: 80px;
                    left: 50%;
                    transform: translateX(-50%);
                }

                .player-hand {
                    position: absolute;
                    bottom: 80px;
                    left: 50%;
                    transform: translateX(-50%);
                }

                /* Kártyák */
                .card {
                    width: 120px;
                    height: 180px;
                    border: 3px solid black;
                    background: white;
                    padding: 8px;
                    box-sizing: border-box;
                    font-size: 14px;
                    text-align: center;
                }

                .card.back 
                {
                    background: #ddd;
                }
                .card.selected 
                {
                    border: 3px solid red;
                    box-shadow: 0 0 10px red;
                    transform: scale(1.05);
                }
                .stat-buttons
                {
                    position: absolute;
                    bottom: 20px;
                    left: 50%;
                    transform: translateX(-50%);
                    display: flex;
                    gap: 15px;
                }
                .stat-buttons button 
                {
                    padding: 10px 20px;
                    font-size: 16px;
                    cursor: pointer;
                }

                .stat-buttons button.selected 
                {
                    background: black;
                    color: white;
                }
    </style>
</head>
<body>

<div class="game-area">

    <!-- Ellenfél pakli -->
    <div class="deck enemy-deck">Ellenfél Pakli</div>

    <!-- Ellenfél lapjai -->
    <div class="hand enemy-hand" id="enemy-hand"></div>

    <!-- Játékos lapjai -->
    <div class="hand player-hand" id="player-hand"></div>
    <div class="stat-buttons">
        <button data-stat="attack">Attack</button>
        <button data-stat="controll">Controll</button>
        <button data-stat="defence">Defence</button>
    </div>


    <!-- Játékos pakli -->
    <div class="deck player-deck" id="player-deck">Játékos Pakli</div>

</div>

<script>
    const players = <?php echo $playersJson; ?>;
</script>







<script>
    let selectedCardIndex = null;
    let selectedStat = null;
    const playerDeck = document.getElementById("player-deck");
    const playerHand = document.getElementById("player-hand");
    const enemyHand = document.getElementById("enemy-hand");

    let playerCards = [];
    let enemyCards = [];

    playerDeck.addEventListener("click", dealCards);

    function shuffle(array) {
        return [...array].sort(() => Math.random() - 0.5);
    }

    function dealCards() {
        playerHand.innerHTML = "";
        enemyHand.innerHTML = "";

        const shuffled = shuffle(players);

        playerCards = shuffled.slice(0, 5);
        enemyCards = shuffled.slice(5, 10);

        renderHands();
    }

    function renderHands() {

        // Ellenfél lapjai (hátoldal)
        enemyCards.forEach(() => {
            const card = document.createElement("div");
            card.className = "card back";
            enemyHand.appendChild(card);
        });

        // Játékos lapjai
        playerCards.forEach((player, index) => {
            const card = document.createElement("div");
            card.className = "card";
            card.innerHTML = `
                <strong>${player.name}</strong><br><br>
                ATK: ${player.attack}<br>
                CTRL: ${player.controll}<br>
                DEF: ${player.defence}
            `;
        card.addEventListener("click", () => {
            selectCard(index);
    });
        playerHand.appendChild(card);
    });
    }
    function selectCard(index) 
    {
        // Régi kijelölés törlése
        const allCards = document.querySelectorAll(".player-hand .card");
        allCards.forEach(card => card.classList.remove("selected"));

        // Új kijelölés
        selectedCardIndex = index;
        allCards[index].classList.add("selected");

        console.log("Kiválasztott lap:", playerCards[index]);
    }
    document.querySelectorAll(".stat-buttons button").forEach(button => {
    button.addEventListener("click", () => {

        if (selectedCardIndex === null) {
            alert("Előbb válassz ki egy kártyát!");
            return;
        }

        selectedStat = button.dataset.stat;

        // vizuális kijelölés
        document.querySelectorAll(".stat-buttons button")
            .forEach(btn => btn.classList.remove("selected"));

        button.classList.add("selected");

        console.log("Kiválasztott stat:", selectedStat);
        console.log("Érték:", playerCards[selectedCardIndex][selectedStat]);
    });
});


</script>


</body>
</html>
