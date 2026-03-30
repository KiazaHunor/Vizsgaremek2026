<?php
header('Content-Type: application/json; charset=utf-8');

$rss_url = "https://www.fociclub.hu/?cat=11&feed=rss2";

// RSS betöltése
$rss = @simplexml_load_file($rss_url);

if (!$rss) {
    echo json_encode(["error" => "Nem sikerült betölteni az RSS feedet"]);
    exit;
}

$news = [];
$i = 0;

foreach ($rss->channel->item as $item) {
    if ($i >= 6) break; // max 6 hír

    $news[] = [
        "title" => (string)$item->title,
        "link"  => (string)$item->link,
        "desc"  => substr(strip_tags((string)$item->description), 0, 150) . "..."
    ];

    $i++;
}

echo json_encode($news, JSON_UNESCAPED_UNICODE);
?>
