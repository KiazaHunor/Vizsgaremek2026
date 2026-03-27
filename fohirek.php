<?php
header('Content-Type: application/json; charset=utf-8');

function getPageContent($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    $html = curl_exec($ch);
    curl_close($ch);

    return $html ?: "";
}

function getOgImage($url) {
    $html = getPageContent($url);
    if (!$html) return "";

    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    $meta = $xpath->query("//meta[@property='og:image']");
    if ($meta->length > 0) {
        return $meta->item(0)->getAttribute('content');
    }

    return "";
}

$rss_url = "https://www.fociclub.hu/?cat=11&feed=rss2";
$rss = @simplexml_load_file($rss_url);

if (!$rss) {
    echo json_encode(["error" => "Nem sikerült betölteni az RSS feedet"]);
    exit;
}

$news = [];
$i = 0;

foreach ($rss->channel->item as $item) {
    if ($i >= 6) break;

    $link = (string)$item->link;
    $image = "";

    // 1. enclosure
    if (isset($item->enclosure)) {
        $attrs = $item->enclosure->attributes();
        if (isset($attrs['url'])) {
            $image = (string)$attrs['url'];
        }
    }

    // 2. media namespace
    if (empty($image)) {
        $media = $item->children('media', true);

        if (isset($media->content)) {
            $attrs = $media->content->attributes();
            if (isset($attrs['url'])) {
                $image = (string)$attrs['url'];
            }
        }

        if (empty($image) && isset($media->thumbnail)) {
            $attrs = $media->thumbnail->attributes();
            if (isset($attrs['url'])) {
                $image = (string)$attrs['url'];
            }
        }
    }

    // 3. fallback: cikkoldalból og:image
    if (empty($image)) {
        $image = getOgImage($link);
    }

    $news[] = [
        "title" => (string)$item->title,
        "link"  => $link,
        "desc"  => mb_substr(strip_tags((string)$item->description), 0, 150) . "...",
        "image" => $image
    ];

    $i++;
}

echo json_encode($news, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>