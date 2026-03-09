<?php
require_once 'db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $positionCode = $_GET['position'] ?? '';
    $excludeRaw = $_GET['exclude'] ?? '';

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

    $excludeIds = [];
    if (!empty($excludeRaw)) {
        $excludeIds = array_values(array_filter(array_map('intval', explode(',', $excludeRaw))));
    }

    $sql = "
        SELECT 
            p.id,
            TRIM(p.name) AS name,
            pos.name AS position,
            t.name AS team,
            n.name AS nationality
        FROM players p
        INNER JOIN positions pos ON p.position_id = pos.id
        INNER JOIN teams t ON p.team_id = t.id
        INNER JOIN nationalities n ON p.nationality_id = n.id
        WHERE pos.name = ?
    ";

    $params = [$positionName];

    if (!empty($excludeIds)) {
        $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));
        $sql .= " AND p.id NOT IN ($placeholders)";
        $params = array_merge($params, $excludeIds);
    }

    $sql .= " ORDER BY RAND() LIMIT 5";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);

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