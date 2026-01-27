<?php
// Adatbázis kapcsolat
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

// 1. Hozzáadjuk az új mezőket, ha még nem léteznek
$fields = ['attack', 'controll', 'defence'];
foreach ($fields as $field) {
    // MySQL 8+ verzióban működik, ha régebbi verzió: előbb ellenőrizni kell
    $pdo->exec("ALTER TABLE players ADD COLUMN IF NOT EXISTS $field INT DEFAULT 0");
}

// 2. Lekérjük az összes játékost
$players = $pdo->query("SELECT id, position FROM players")->fetchAll();

// 3. Pozícióhoz tartozó értéktartományok
$positionRanges = [
    'Goalkeeper' => ['attack'=>[5,15], 'controll'=>[15,30], 'defence'=>[75,85]],
    'Left-Back'   => ['attack'=>[45,55], 'controll'=>[55,65], 'defence'=>[65,75]],
    'Right-Back'  => ['attack'=>[45,55], 'controll'=>[55,65], 'defence'=>[65,75]],
    'Centre-Back' => ['attack'=>[35,45], 'controll'=>[45,55], 'defence'=>[75,90]],
    'Defensive Midfield' => ['attack'=>[40,50], 'controll'=>[60,70], 'defence'=>[65,80]],
    'Right Midfield' => ['attack'=>[60,70], 'controll'=>[70,80], 'defence'=>[50,60]],
    'Left Midfield'  => ['attack'=>[60,70], 'controll'=>[70,80], 'defence'=>[50,60]],
    'Central Midfield' => ['attack'=>[55,65], 'controll'=>[75,90], 'defence'=>[55,65]],
    'Attacking Midfield' => ['attack'=>[65,75], 'controll'=>[75,90], 'defence'=>[45,55]],
    'Left Winger'   => ['attack'=>[70,85], 'controll'=>[75,85], 'defence'=>[35,45]],
    'Right Winger'  => ['attack'=>[70,85], 'controll'=>[75,85], 'defence'=>[35,45]],
    'Midfielder'  => ['attack'=>[55,65], 'controll'=>[75,90], 'defence'=>[45,55]],
    'Centre-Forward' => ['attack'=>[85,95], 'controll'=>[65,75], 'defence'=>[25,35]],
];

// 4. Frissítjük minden rekordot
$stmt = $pdo->prepare("UPDATE players SET attack = ?, controll = ?, defence = ? WHERE id = ?");

foreach ($players as $player) {
    $pos = $player['position'] ?? ''; // ha üres
    if (isset($positionRanges[$pos])) {
        $range = $positionRanges[$pos];
        $attack = rand($range['attack'][0], $range['attack'][1]);
        $controll = rand($range['controll'][0], $range['controll'][1]);
        $defence = rand($range['defence'][0], $range['defence'][1]);
    } else {
        // Default tartomány minden ismeretlen pozícióra
        $attack = rand(40,60);
        $controll = rand(40,60);
        $defence = rand(40,60);
    }
    $stmt->execute([$attack, $controll, $defence, $player['id']]);
}

echo "Minden rekord frissítve lett véletlenszerű statokkal.";
