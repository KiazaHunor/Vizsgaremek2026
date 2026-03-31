<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli("localhost", "root", "", "fizzliga_db");

if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Adatbázis kapcsolódási hiba: " . $conn->connect_error
    ]);
    exit;
}

$sql = "SELECT id, result, player_score, enemy_score, played_at 
        FROM card_game_results
        ORDER BY played_at DESC";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "success" => false,
        "message" => "Lekérdezési hiba: " . $conn->error
    ]);
    exit;
}

$results = [];
$wins = 0;
$draws = 0;
$losses = 0;

while ($row = $result->fetch_assoc()) {
    $results[] = $row;

    if ($row["result"] === "win") $wins++;
    if ($row["result"] === "draw") $draws++;
    if ($row["result"] === "loss") $losses++;
}

echo json_encode([
    "success" => true,
    "summary" => [
        "total" => count($results),
        "wins" => $wins,
        "draws" => $draws,
        "losses" => $losses
    ],
    "results" => $results
]);

$conn->close();
?>