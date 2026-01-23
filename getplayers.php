<?php
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$pass = "";
$db   = "fizzliga_dbproba";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

if (!isset($_GET['position']) || empty($_GET['position'])) {
    die(json_encode(["error" => "No position provided"]));
}

$position = $_GET['position'];

// Véletlenszerű 5 játékos lekérése
$sql = "SELECT name, team, position, nationality FROM players WHERE position = ? ORDER BY RAND() LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $position);
$stmt->execute();

$result = $stmt->get_result();
$players = [];
while ($row = $result->fetch_assoc()) {
    $row['name'] = ucfirst($row['name']);
    $players[] = $row;
}

echo json_encode($players);
