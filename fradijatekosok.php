<?php
require_once 'db.php';

$teamName = 'Ferencváros Budapest';

$sql = "
    SELECT 
        p.name AS player_name,
        n.name AS nationality,
        pos.name AS position,
        s.attack,
        s.controll,
        s.defence
    FROM player_stats s
    JOIN players p ON s.player_id = p.id
    JOIN teams t ON p.team_id = t.id
    JOIN nationalities n ON p.nationality_id = n.id
    JOIN positions pos ON p.position_id = pos.id
    WHERE t.name = ?
    ORDER BY p.name
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$teamName]);
$players = $stmt->fetchAll();
?>

<h2><?= htmlspecialchars($teamName) ?> játékosai + statok</h2>
<ul>
<?php foreach ($players as $p): ?>
    <li>
        <?= htmlspecialchars($p['player_name']) ?> – <?= htmlspecialchars($p['nationality']) ?> 
        (<?= htmlspecialchars($p['position']) ?>)
        <br>
        Attack: <?= $p['attack'] ?>, Controll: <?= $p['controll'] ?>, Defence: <?= $p['defence'] ?>
    </li>
<?php endforeach; ?>
</ul>
