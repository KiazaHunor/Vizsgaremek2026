<?php
require_once 'db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $positionCode = $_GET['position'] ?? '';
    $excludeRaw = $_GET['exclude'] ?? '';

    $positionMap = [
        'GK'  => ['Goalkeeper'],
        'CB'  => ['Centre-Back'],
        'LB'  => ['Left-Back'],
        'RB'  => ['Right-Back'],
        'CDM' => ['Defensive Midfield'],
        'CM'  => ['Central Midfield'],
        'CAM' => ['Attacking Midfield'],

        'LM'  => ['Left Midfield', 'Left Winger'],
        'RM'  => ['Right Midfield', 'Right Winger'],

        'LW'  => ['Left Winger', 'Left Midfield'],
        'RW'  => ['Right Winger', 'Right Midfield'],

        'CF'  => ['Centre-Forward']
    ];

    $excludeIds = [];
    if (!empty($excludeRaw)) {
        $excludeIds = array_values(
            array_filter(
                array_map('intval', explode(',', $excludeRaw))
            )
        );
    }

    $sql = "
        SELECT
            p.id,
            TRIM(p.name) AS name,
            pos.name AS position,
            t.name AS team,
            n.name AS nationality,
            COALESCE((
                SELECT pr.rating
                FROM player_ratings pr
                WHERE pr.player_id = p.id
                ORDER BY pr.updated_at DESC, pr.id DESC
                LIMIT 1
            ), 0) AS rating,
            (
                SELECT tk.image_path
                FROM team_kits tk
                WHERE tk.team_id = t.id
                ORDER BY tk.id DESC
                LIMIT 1
            ) AS shirt_image
        FROM players p
        INNER JOIN positions pos ON p.position_id = pos.id
        INNER JOIN teams t ON p.team_id = t.id
        INNER JOIN nationalities n ON p.nationality_id = n.id
    ";

    $params = [];
    $conditions = [];

    if ($positionCode === 'CSERE' || $positionCode === 'TART') {
        $conditions[] = "1=1";
    } else {
        if (!isset($positionMap[$positionCode])) {
            echo json_encode([
                'success' => false,
                'message' => 'Érvénytelen pozíció.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $selectedPositions = $positionMap[$positionCode];
        $positionPlaceholders = implode(',', array_fill(0, count($selectedPositions), '?'));
        $conditions[] = "pos.name IN ($positionPlaceholders)";
        $params = array_merge($params, $selectedPositions);
    }

    if (!empty($excludeIds)) {
        $excludePlaceholders = implode(',', array_fill(0, count($excludeIds), '?'));
        $conditions[] = "p.id NOT IN ($excludePlaceholders)";
        $params = array_merge($params, $excludeIds);
    }

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " ORDER BY RAND() LIMIT 5";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($players as &$player) {
        $player['rating'] = (float)$player['rating'];
    }
    unset($player);

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