 function loadNews() {
      fetch("../hirek.php")
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

    //TABELLA



    function loadTabella() {
      fetch("../tabella.php")
        .then(r => r.json())
        .then(data => {
          if (data.error) {
            document.getElementById("tabella").innerHTML =
              `<div class="alert alert-danger">${data.error}</div>`;
            return;
          }

          let html = `<table class="table table-dark table-striped">
      <thead>
        <tr>
          <th>#</th>
          <th>Csapat</th>
          <th>M</th>
          <th>Gy</th>
          <th>D</th>
          <th>V</th>
          <th>Pont</th>
        </tr>
      </thead><tbody>`;

          data.forEach(r => {
            html += `<tr>
          <td>${r.hely}</td>
          <td>${r.csapat}</td>
          <td>${r.meccs}</td>
          <td>${r.gy}</td>
          <td>${r.d}</td>
          <td>${r.v}</td>
          <td>${r.pont}</td>
        </tr>`;
          });

          html += "</tbody></table>";
          document.getElementById("tabella").innerHTML = html;
        })
        .catch(() => {
          document.getElementById("tabella").innerHTML =
            `<div class="alert alert-danger">Betöltési hiba</div>`;
        });
    }

    loadTabella();
    setInterval(loadTabella, 60000);

    