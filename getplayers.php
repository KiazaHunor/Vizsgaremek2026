<?php
header('Content-Type: application/json');

// Adatbázis kapcsolat
$host = "localhost";
$user = "root";
$pass = "";
$db   = "fizzliga_dbproba";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Ellenőrizzük, kaptunk-e pozíciót
if (!isset($_GET['position']) || empty($_GET['position'])) {
    die(json_encode(["error" => "No position provided"]));
}

$position = $_GET['position'];

// Lekérdezés
$sql = "SELECT name FROM players WHERE position = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $position);
$stmt->execute();

$result = $stmt->get_result();
$players = [];
while ($row = $result->fetch_assoc()) {
    $row['name'] = ucfirst($row['name']); // első betű nagy
    $players[] = $row;
}

echo json_encode($players);
