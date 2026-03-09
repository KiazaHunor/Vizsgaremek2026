<?php
require_once 'playersdata.php';

try {
    // 1. Lekérjük az összes játékost a pozíciójával
    $sql = "
        SELECT 
            p.id AS player_id,
            pos.name AS position
        FROM players p
        INNER JOIN positions pos ON p.position_id = pos.id
    ";

    $players = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    // 2. Pozícióhoz tartozó stat tartományok
    $positionRanges = [
        'Goalkeeper'         => ['attack' => [5, 15],  'controll' => [15, 30], 'defence' => [75, 95]],
        'Left-Back'          => ['attack' => [45, 55], 'controll' => [55, 65], 'defence' => [65, 90]],
        'Right-Back'         => ['attack' => [45, 55], 'controll' => [55, 65], 'defence' => [65, 90]],
        'Centre-Back'        => ['attack' => [35, 45], 'controll' => [45, 55], 'defence' => [75, 90]],
        'Defensive Midfield' => ['attack' => [40, 50], 'controll' => [60, 70], 'defence' => [65, 80]],
        'Right Midfield'     => ['attack' => [60, 70], 'controll' => [70, 80], 'defence' => [50, 60]],
        'Left Midfield'      => ['attack' => [60, 70], 'controll' => [70, 80], 'defence' => [50, 60]],
        'Central Midfield'   => ['attack' => [55, 65], 'controll' => [75, 90], 'defence' => [55, 65]],
        'Attacking Midfield' => ['attack' => [65, 75], 'controll' => [75, 90], 'defence' => [45, 55]],
        'Left Winger'        => ['attack' => [70, 85], 'controll' => [75, 85], 'defence' => [35, 45]],
        'Right Winger'       => ['attack' => [70, 85], 'controll' => [75, 85], 'defence' => [35, 45]],
        'Midfielder'         => ['attack' => [55, 65], 'controll' => [75, 90], 'defence' => [45, 55]],
        'Centre-Forward'     => ['attack' => [85, 95], 'controll' => [65, 75], 'defence' => [25, 35]],
    ];

    // 3. Insert vagy update player_stats-ba
    $stmt = $pdo->prepare("
        INSERT INTO player_stats (player_id, attack, controll, defence)
        VALUES (:player_id, :attack, :controll, :defence)
        ON DUPLICATE KEY UPDATE
            attack = VALUES(attack),
            controll = VALUES(controll),
            defence = VALUES(defence),
            updated_at = CURRENT_TIMESTAMP
    ");

    $pdo->beginTransaction();

    foreach ($players as $player) {
        $pos = trim($player['position'] ?? '');

        if (isset($positionRanges[$pos])) {
            $range = $positionRanges[$pos];

            $attack   = random_int($range['attack'][0], $range['attack'][1]);
            $controll = random_int($range['controll'][0], $range['controll'][1]);
            $defence  = random_int($range['defence'][0], $range['defence'][1]);
        } else {
            // fallback ismeretlen pozícióra
            $attack   = random_int(40, 60);
            $controll = random_int(40, 60);
            $defence  = random_int(40, 60);
        }

        $stmt->execute([
            ':player_id' => $player['player_id'],
            ':attack'    => $attack,
            ':controll'  => $controll,
            ':defence'   => $defence,
        ]);
    }

    $pdo->commit();

    echo "A player_stats tábla sikeresen feltöltve/frissítve lett.";
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo "Hiba történt: " . $e->getMessage();
}