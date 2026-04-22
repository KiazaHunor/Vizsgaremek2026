<?php
require_once 'db.php';

/*
========================================
1. OPEN → CLOSED (nevezés lezárása)
========================================
*/
$closedCount = $pdo->exec("
    UPDATE tournaments
    SET status = 'closed'
    WHERE status = 'open' AND entry_deadline <= NOW()
");

/*
========================================
2. CLOSED → FINISHED (eredmény + reward)
========================================
*/
$stmt = $pdo->query("
    SELECT id 
    FROM tournaments
    WHERE status = 'closed' AND result_at <= NOW()
");

$tournaments = $stmt->fetchAll();

foreach ($tournaments as $t) {

    $tournamentId = $t['id'];

    echo "Lezárás: Tournament ID = $tournamentId <br>";

    // 🔽 LEADERBOARD
    $stmtEntries = $pdo->prepare("
        SELECT *
        FROM tournament_entries
        WHERE tournament_id = ?
        ORDER BY 
            final_score DESC,
            chemistry_score DESC,
            rating_avg_score DESC,
            submitted_at ASC
    ");
    $stmtEntries->execute([$tournamentId]);

    $entries = $stmtEntries->fetchAll();

    $rank = 1;

    foreach ($entries as $entry) {

        if ($rank == 1) $credits = 1000;
        else if ($rank == 2) $credits = 850;
        else if ($rank == 3) $credits = 750;
        else if ($rank == 4) $credits = 650;
        else if ($rank == 5) $credits = 500;
        else $credits = 200;

        $pdo->prepare("
            UPDATE tournament_entries
            SET rank_position = ?, credits_awarded = ?
            WHERE id = ?
        ")->execute([$rank, $credits, $entry['id']]);

        $pdo->prepare("
            UPDATE users
            SET credit = credit + ?
            WHERE id = ?
        ")->execute([$credits, $entry['user_id']]);

        $rank++;
    }

    // 🔥 státusz finished
    $pdo->prepare("
        UPDATE tournaments
        SET status = 'finished'
        WHERE id = ?
    ")->execute([$tournamentId]);

    echo "Kész: Tournament $tournamentId finished <br>";
}

echo "<br>OK - státusz frissítés kész.";