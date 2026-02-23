<?php
require_once '../db.php';

// 1. Lekérjük az összes játékost a pozíciójukkal
$sql = "
    SELECT p.id AS player_id, pos.name AS position
    FROM players p
    JOIN positions pos ON p.position_id = pos.id
";
$players = $pdo->query($sql)->fetchAll();

// 2. Pozícióhoz tartozó stat tartományok
$positionRanges = [
    'Goalkeeper' => ['attack'=>[5,15], 'controll'=>[15,30], 'defence'=>[75,95]],
    'Left-Back'   => ['attack'=>[45,55], 'controll'=>[55,65], 'defence'=>[65,90]],
    'Right-Back'  => ['attack'=>[45,55], 'controll'=>[55,65], 'defence'=>[65,90]],
    'Centre-Back' => ['attack'=>[35,45], 'controll'=>[45,55], 'defence'=>[75,90]],
    'Defensive Midfield' => ['attack'=>[40,50], 'controll'=>[60,70], 'defence'=>[65,80]],
    'Right Midfield' => ['attack'=>[60,70], 'controll'=>[70,80], 'defence'=>[50,60]],
    'Left Midfield'  => ['attack'=>[60,70], 'controll'=>[70,80], 'defence'=>[50,60]],
    'Central Midfield' => ['attack'=>[55,65], 'controll'=>[75,90], 'defence'=>[55,65]],
    'Attacking Midfield' => ['attack'=>[65,75], 'controll'=>[75,90], 'defence'=>[45,55]],
    'Left Winger'   => ['attack'=>[70,85], 'controll'=>[75,85], 'defence'=>[35,45]],
    'Right Winger'  => ['attack'=>[70,85], 'controll'=>[75,85], 'defence'=>[35,45]],
    'Midfielder'    => ['attack'=>[55,65], 'controll'=>[75,90], 'defence'=>[45,55]],
    'Centre-Forward'=> ['attack'=>[85,95], 'controll'=>[65,75], 'defence'=>[25,35]],
];

// 3. Prepared statement – player_stats tábla
$stmt = $pdo->prepare("
    INSERT INTO player_stats (player_id, attack, controll, defence)
    VALUES (?, ?, ?, ?)
");

// 4. Generáljuk a statokat
foreach ($players as $player) {
    $pos = $player['position'] ?? '';

    if (isset($positionRanges[$pos])) {
        $range = $positionRanges[$pos];
        $attack   = rand($range['attack'][0], $range['attack'][1]);
        $controll = rand($range['controll'][0], $range['controll'][1]);
        $defence  = rand($range['defence'][0], $range['defence'][1]);
    } else {
        // Default tartomány minden ismeretlen pozícióra
        $attack   = rand(40,60);
        $controll = rand(40,60);
        $defence  = rand(40,60);
    }

    // INSERT player_stats
    $stmt->execute([$player['player_id'], $attack, $controll, $defence]);
}

echo "Minden játékoshoz generálva lett a stat rekord az új táblában.\n";
