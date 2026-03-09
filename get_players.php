<?php
require_once 'db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $positionCode = $_GET['position'] ?? '';

    $positionMap = [
        'GK'  => 'Goalkeeper',
        'CB'  => 'Centre-Back',
        'LB'  => 'Left-Back',
        'RB'  => 'Right-Back',
        'CDM' => 'Defensive Midfield',
        'CM'  => 'Central Midfield',
        'CAM' => 'Attacking Midfield',
        'LM'  => 'Left Midfield',
        'RM'  => 'Right Midfield',
        'LW'  => 'Left Winger',
        'RW'  => 'Right Winger',
        'CF'  => 'Centre-Forward'
    ];

    if (!isset($positionMap[$positionCode])) {
        echo json_encode([
            'success' => false,
            'message' => 'Érvénytelen pozíció.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $positionName = $positionMap[$positionCode];

  $sql = "
    SELECT 
        p.id,
        TRIM(p.name) AS name,
        t.name AS team,
        n.name AS nationality
    FROM players p
    INNER JOIN positions pos ON p.position_id = pos.id
    INNER JOIN teams t ON p.team_id = t.id
    INNER JOIN nationalities n ON p.nationality_id = n.id
    WHERE pos.name = :position_name
    ORDER BY RAND()
    LIMIT 5
";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':position_name' => $positionName
    ]);

    $players = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'players' => $players
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}