function loadNews() {
    fetch("fohirek.php")
        .then(r => r.json())
        .then(data => {
            const newsContainer = document.getElementById("news-container");
            newsContainer.innerHTML = "";

            if (data.error) {
                newsContainer.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                return;
            }

            const row = document.createElement("div");
            row.className = "row g-4";

            data.forEach(item => {
                const col = document.createElement("div");
                col.className = "col-12 col-md-6 col-xl-4";

                col.innerHTML = `
                    <div class="card news-card h-100" onclick="window.open('${item.link}', '_blank')" style="cursor:pointer;">
                        ${item.image ? `<img src="${item.image}" class="card-img-top news-img" alt="${item.title}">` : ""}
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">${item.title}</h5>
                            <p class="card-text">${item.description ? item.description : "Nincs leírás."}</p>
                            <div class="mt-auto pt-2">
                                <small class="text-white">${item.pubDate ? item.pubDate : ""}</small>
                            </div>
                        </div>
                    </div>
                `;

                row.appendChild(col);
            });

            newsContainer.appendChild(row);
        })
        .catch(() => {
            document.getElementById("news-container").innerHTML =
                `<div class="alert alert-danger">Nem sikerült betölteni a híreket.</div>`;
        });
}

loadNews();
setInterval(loadNews,6000);