document.addEventListener("DOMContentLoaded", function () {
  const footerTarget = document.getElementById("footer-container");
  if (!footerTarget) return;

  fetch(getFooterPath())
    .then(function (response) {
      if (!response.ok) {
        die("Nem sikerült betölteni a footert.");
      }
      return response.text();
    })
    .then(function (html) {
      footerTarget.innerHTML = html;

      if (typeof updateMenuAuth === "function") {
        updateMenuAuth();
      }

      if (typeof initThemeToggle === "function") {
        initThemeToggle();
      }
    })
    .catch(function (error) {
      console.error("Footer betöltési hiba:", error);
    });
});

function getFooterPath() {
  if (window.location.pathname.includes("/kapcsolat/")) {
    return "../footer.html";
  }

  return "footer.html";
}