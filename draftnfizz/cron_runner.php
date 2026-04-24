<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "START<br>";

ob_start();
include __DIR__ . '/create_daily_tournament.php';
$result1 = ob_get_clean();
echo "CREATE: " . htmlspecialchars($result1 ?: "lefutott") . "<br>";

ob_start();
include __DIR__ . '/update_tournament_statuses.php';
$result2 = ob_get_clean();
echo "UPDATE: " . htmlspecialchars($result2 ?: "lefutott") . "<br>";

echo "OK";