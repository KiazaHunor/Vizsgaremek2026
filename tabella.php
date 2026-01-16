<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$cache_file = 'cache_nb1.json';
$cache_time = 300; // 5 perc

if(file_exists($cache_file) && time() - filemtime($cache_file) < $cache_time){
    echo file_get_contents($cache_file);
    exit;
}

// Lekérés az Eurosport oldalról
$url = "https://www.eurosport.hu/labdarugas/otp-bank-liga/allasok.shtml";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
$html = curl_exec($ch);
curl_close($ch);

if (!$html) {
    echo json_encode(["error" => "Nem sikerült letölteni az oldalt"]);
    exit;
}

// DOM parse
$doc = new DOMDocument();
libxml_use_internal_errors(true);
$doc->loadHTML($html);
libxml_clear_errors();

$tables = $doc->getElementsByTagName("table");
if ($tables->length === 0) {
    echo json_encode(["error" => "Táblázat nem található"]);
    exit;
}

$table = $tables->item(0);
$rows = $table->getElementsByTagName("tr");

$data = [];
foreach ($rows as $row) {
    $cols = $row->getElementsByTagName("td");
    if ($cols->length > 0) {
        $data[] = [
            "hely" => trim($cols->item(0)->textContent),
            "csapat" => trim($cols->item(1)->textContent),
            "meccs" => trim($cols->item(2)->textContent),
            "gy" => trim($cols->item(3)->textContent),
            "d" => trim($cols->item(4)->textContent),
            "v" => trim($cols->item(5)->textContent),
            "pont" => trim($cols->item(9)->textContent),
        ];
    }
}

// Cache mentése
file_put_contents($cache_file, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

// Visszaadás JSON-ként
echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
