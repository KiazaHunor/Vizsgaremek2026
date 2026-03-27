function loadNews() {
  fetch("../fohirek.php")
    .then(r => r.json())
    .then(data => {
      const newsContainer = document.getElementById("news-container");
      newsContainer.innerHTML = "";

      if (data.error) {
        newsContainer.innerHTML =
          `<div class="alert alert-danger">${data.error}</div>`;
        return;
      }

      data.forEach(item => {
        const col = document.createElement("div");
        col.className = "col-12 mb-4";

        col.innerHTML = `
          <div class="card news-card h-100" onclick="window.open('${item.link}', '_blank')">
            ${item.image ? `<img src="${item.image}" class="card-img-top news-img" alt="${item.title}">` : ""}
            <div class="card-body">
              <h5 class="card-title">${item.title}</h5>
              <p class="card-text">${item.desc}</p>
            </div>
          </div>
        `;

        newsContainer.appendChild(col);
      });
    })
    .catch(() => {
      document.getElementById("news-container").innerHTML =
        `<div class="alert alert-danger">Hiba a hírek betöltésénél</div>`;
    });
}

loadNews();
setInterval(loadNews, 60000);