<?php
// Adatbázis beállítások
$host = 'localhost';
$db   = 'fizzliga_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("DB kapcsolat hiba: " . $e->getMessage());
}

// Segédfüggvény: cURL lekérés
function fetchUrl($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    $html = curl_exec($ch);
    curl_close($ch);
    return $html;
}

// NB I csapatok Transfermarkt URL-jei
$teams = [
      "Ferencváros Budapest" => "https://www.transfermarkt.com/ferencvaros-budapest/startseite/verein/279/saison_id/2025",
    "Újpest FC" => "https://www.transfermarkt.com/ujpest-fc/startseite/verein/708/saison_id/2025",
    "Puskás Akadémia FC" => "https://www.transfermarkt.com/puskas-akademia-fc/startseite/verein/37169/saison_id/2025",
    "ETO FC Győr" => "https://www.transfermarkt.com/eto-fc-gyor/startseite/verein/6055/saison_id/2025",
    "MTK Budapest" => "https://www.transfermarkt.com/mtk-budapest/startseite/verein/634/saison_id/2025",
    "Debreceni VSC" => "https://www.transfermarkt.com/debreceni-vsc/startseite/verein/84/saison_id/2025",
    "Paksi FC" => "https://www.transfermarkt.com/paksi-fc/startseite/verein/12163/saison_id/2025",
    "Nyíregyháza Spartacus" => "https://www.transfermarkt.com/nyiregyhaza-spartacus/startseite/verein/6058/saison_id/2025",
    "Diósgyőri VTK" => "https://www.transfermarkt.com/diosgyori-vtk/startseite/verein/9241/saison_id/2025",
    "Zalaegerszegi TE FC" => "https://www.transfermarkt.com/zalaegerszegi-te-fc/startseite/verein/1391/saison_id/2025",
    "Kisvárda FC" => "https://www.transfermarkt.com/kisvarda-fc/startseite/verein/30613/saison_id/2025",
    "Kazincbarcikai SC" => "https://www.transfermarkt.com/kazincbarcikai-sc/startseite/verein/24031/saison_id/2025"
];

// Játékosok kigyűjtése csapatoldalról
function scrapeTeamPlayers($url, $teamName) {
    $html = fetchUrl($url);
    if (!$html) return [];

    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $players = [];

    // Csak a felnőtt keret sorai (odd/even class)
    $rows = $xpath->query("//table[contains(@class,'items')]/tbody/tr[contains(@class,'odd') or contains(@class,'even')]");

    foreach ($rows as $row) {
        // Név
        $nameNode = $xpath->query(".//td[@class='hauptlink']//a[1]", $row)->item(0);
        $name = trim($nameNode->textContent ?? '');
        if (!$name) continue;

        // Nemzetiség
        $natNode = $xpath->query(".//td[@class='zentriert']//img[@title]", $row)->item(0);
        $nationality = $natNode ? $natNode->getAttribute('title') : '';

        // Pozíció
        $posrela = $xpath->query(".//td[contains(@class,'posrela')]", $row)->item(0);
        $position = '';
        if ($posrela) {
            $innerPos = $xpath->query(".//table//tr[2]/td", $posrela)->item(0);
            if ($innerPos) {
                $position = trim($innerPos->textContent);
            }
        }

        $players[] = [
            'team' => $teamName,
            'name' => $name,
            'position' => $position,
            'nationality' => $nationality
        ];
    }

    return $players;
}

// Feldolgozás és adatbázisba mentés
foreach ($teams as $teamName => $url) {
    echo "Lekérdezés: $teamName...\n";
    $players = scrapeTeamPlayers($url, $teamName);

    foreach ($players as $p) {
        $stmt = $pdo->prepare("
            INSERT INTO players (team, name, position, nationality)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $p['team'],
            $p['name'],
            $p['position'],
            $p['nationality']
        ]);
    }

    echo "Hozzáadva: " . count($players) . " játékos a $teamName csapatból.\n";
}

echo "Minden NB I játékos betöltve az adatbázisba.\n";
?>
