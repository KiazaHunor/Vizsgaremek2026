<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Focihírek – Élő frissítés</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #0f172a;
            color: #e5e7eb;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        #news {
            max-width: 900px;
            margin: auto;
        }

        .news-item {
            background: #020617;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            transition: transform 0.2s ease;
        }

        .news-item:hover {
            transform: scale(1.02);
        }

        .news-item a {
            color: #38bdf8;
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
        }

        .news-item a:hover {
            text-decoration: underline;
        }

        .date {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 5px;
        }

        .refresh {
            text-align: center;
            margin: 20px 0;
            font-size: 13px;
            color: #94a3b8;
        }
    </style>
</head>

<body>

<h1>⚽ Friss focihírek</h1>
<div class="refresh">Automatikusan frissül minden betöltéskor</div>

<div id="news"></div>

<script>
    const RSS_URL = "https://feeds.bbci.co.uk/sport/football/rss.xml";
    const API_URL = "https://api.rss2json.com/v1/api.json?rss_url=" + encodeURIComponent(RSS_URL);

    fetch(API_URL)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById("news");

            data.items.slice(0, 10).forEach(item => {
                const div = document.createElement("div");
                div.className = "news-item";

                div.innerHTML = `
                    <a href="${item.link}" target="_blank">${item.title}</a>
                    <div class="date">${new Date(item.pubDate).toLocaleString()}</div>
                `;

                container.appendChild(div);
            });
        })
        .catch(error => {
            document.getElementById("news").innerHTML =
                "<p>Nem sikerült betölteni a híreket.</p>";
            console.error(error);
        });
</script>

</body>
</html>
