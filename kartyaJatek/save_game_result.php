<?php
session_start();
header('Content-Type: application/json');

$host = "localhost";
$dbname = "fizzliga_db";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Adatbázis kapcsolódási hiba!"
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data["result"], $data["player_score"], $data["enemy_score"])) {
    echo json_encode([
        "success" => false,
        "message" => "Hiányzó adatok!"
    ]);
    exit;
}

$result = $data["result"];
$playerScore = (int)$data["player_score"];
$enemyScore = (int)$data["enemy_score"];

// Ha van bejelentkezett user session
$userId = isset($_SESSION["user_id"]) ? (int)$_SESSION["user_id"] : null;

$stmt = $conn->prepare("INSERT INTO card_game_results (user_id, result, player_score, enemy_score) VALUES (?, ?, ?, ?)");

$stmt->bind_param("isii", $userId, $result, $playerScore, $enemyScore);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Eredmény elmentve."
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Mentési hiba: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>