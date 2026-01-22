<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$url = "https://hlsz.hu/tabella"; // HLSZ NB1 tabella URL
$cacheFile = "hlsz_cache.json";
$cacheTime = 300; // 5 perc

// Cache ellenőrzés
if (file_exists($cacheFile) && time() - filemtime($cacheFile) < $cacheTime) {
    echo file_get_contents($cacheFile);
    exit;
}

// cURL lekérés
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_USERAGENT => "Mozilla/5.0"
]);
$html = curl_exec($ch);
curl_close($ch);

if (!$html) {
    echo json_encode(["error" => "Oldal nem tölthető"]);
    exit;
}

libxml_use_internal_errors(true);
$dom = new DOMDocument();
$dom->loadHTML($html);
libxml_clear_errors();

$xpath = new DOMXPath($dom);

// Minden sor lekérése az összes táblázatból
$rows = $xpath->query("//table//tr");

$data = [];
$count = 0;

foreach ($rows as $row) {
    $cols = $row->getElementsByTagName("td");
    if ($cols->length >= 6) {
        $data[] = [
            "hely" => trim($cols->item(0)->textContent),
            "csapat" => trim($cols->item(1)->textContent),
            "meccs" => trim($cols->item(2)->textContent),
            "gy" => trim($cols->item(3)->textContent),
            "d" => trim($cols->item(4)->textContent),
            "v" => trim($cols->item(5)->textContent),
            "pont" => trim($cols->item($cols->length - 1)->textContent)
        ];
        $count++;
        if ($count >= 12) break; // <-- itt állítjuk meg az első 12 sor után
    }
}

if (count($data) === 0) {
    echo json_encode(["error" => "NB1 táblázat nem tartalmaz adatot"]);
    exit;
}

// Cache
file_put_contents($cacheFile, json_encode($data, JSON_UNESCAPED_UNICODE));

// JSON visszaadás
echo json_encode($data, JSON_UNESCAPED_UNICODE);
