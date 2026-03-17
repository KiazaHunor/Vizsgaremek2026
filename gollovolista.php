<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$url = "https://hlsz.hu/kezdolap";
$cacheFile = "gollovolista_cache.json";
$cacheTime = 300;

$nb1Csapatok = [
    "Újpest FC",
    "Puskás Akadémia",
    "Debrecen",
    "ETO FC Győr",
    "ZTE",
    "Ferencvárosi TC",
    "Paks",
    "MTK",
    "Diósgyőr",
    "Nyíregyháza",
    "Kisvárda",
    "Kazincbarcika"
];

if (file_exists($cacheFile) && time() - filemtime($cacheFile) < $cacheTime) {
    echo file_get_contents($cacheFile);
    exit;
}

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
    echo json_encode(["error" => "Az oldal nem tölthető be"], JSON_UNESCAPED_UNICODE);
    exit;
}

libxml_use_internal_errors(true);
$dom = new DOMDocument();
$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
libxml_clear_errors();

$xpath = new DOMXPath($dom);
$data = [];

$titles = $xpath->query("//*[contains(text(), 'Göllövőlista / NB I, 2025/2026')]");

foreach ($titles as $title) {
    $box = $title;
    for ($i = 0; $i < 5; $i++) {
        if ($box->parentNode) {
            $box = $box->parentNode;
        }
    }

    $text = trim($box->textContent);
    $text = preg_replace('/\s+/u', ' ', $text);

    preg_match_all('/(\d+)\.\s+([^(0-9]+?)\s*\(([^)]+)\)\s+(\d+)/u', $text, $matches, PREG_SET_ORDER);

    $temp = [];

    foreach ($matches as $m) {
        $csapat = trim($m[3]);

        if (!in_array($csapat, $nb1Csapatok)) {
            continue;
        }

        $temp[] = [
            "hely" => (int) trim($m[1]),
            "jatekos" => trim($m[2]),
            "csapat" => $csapat,
            "gol" => (int) trim($m[4])
        ];
    }

    if (count($temp) > 0) {
        $data = array_slice($temp, 0, 12);
        break;
    }
}

if (count($data) === 0) {
    echo json_encode(["error" => "Nem sikerült kiolvasni az NB I-es góllövőlistát"], JSON_UNESCAPED_UNICODE);
    exit;
}

file_put_contents($cacheFile, json_encode($data, JSON_UNESCAPED_UNICODE));
echo json_encode($data, JSON_UNESCAPED_UNICODE);