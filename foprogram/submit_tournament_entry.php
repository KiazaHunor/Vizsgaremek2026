<?php
require_once 'db.php';
session_start();

header('Content-Type: application/json; charset=utf-8');

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Be kell jelentkezni.');
    }

    $userId = (int)$_SESSION['user_id'];
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        throw new Exception('Nincs adat elküldve.');
    }

    $tournamentId = (int)($data['tournament_id'] ?? 0);
    $teamName = trim($data['team_name'] ?? '');
    $chemistryScore = (int)($data['chemistry_score'] ?? 0);
    $ratingAvgScore = (int)($data['rating_avg_score'] ?? 0);
    $finalScore = (int)($data['final_score'] ?? 0);
    $players = $data['players'] ?? [];

    if ($tournamentId <= 0) {
        throw new Exception('Hiányzó bajnokság.');
    }

    if (count($players) < 11) {
        throw new Exception('Legalább 11 kezdő kell.');
    }

    $stmt = $pdo->prepare("
        SELECT id
        FROM tournaments
        WHERE id = ?
          AND status = 'open'
          AND NOW() BETWEEN start_at AND entry_deadline
        LIMIT 1
    ");
    $stmt->execute([$tournamentId]);
    $tournament = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tournament) {
        throw new Exception('Ez a bajnokság már nem nevezhető.');
    }

    $stmt = $pdo->prepare("
        SELECT id
        FROM tournament_entries
        WHERE tournament_id = ? AND user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$tournamentId, $userId]);

    if ($stmt->fetch()) {
        throw new Exception('Erre a bajnokságra már neveztél.');
    }

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO tournament_entries
        (tournament_id, user_id, team_name, chemistry_score, rating_avg_score, final_score, submitted_at, is_locked)
        VALUES (?, ?, ?, ?, ?, ?, NOW(), 1)
    ");
    $stmt->execute([
        $tournamentId,
        $userId,
        $teamName,
        $chemistryScore,
        $ratingAvgScore,
        $finalScore
    ]);

    $entryId = (int)$pdo->lastInsertId();

    $stmtPlayer = $pdo->prepare("
        INSERT INTO tournament_entry_players
        (entry_id, player_id, slot_code, is_starter, chemistry_points)
        VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($players as $player) {
        $playerId = (int)($player['player_id'] ?? 0);
        $slotCode = trim($player['slot_code'] ?? '');
        $isStarter = (int)($player['is_starter'] ?? 1);
        $chemistryPoints = (int)($player['chemistry_points'] ?? 0);

        if ($playerId <= 0 || $slotCode === '') {
            throw new Exception('Hibás játékos adat.');
        }

        $stmtPlayer->execute([
            $entryId,
            $playerId,
            $slotCode,
            $isStarter,
            $chemistryPoints
        ]);
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Sikeres nevezés.'
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}