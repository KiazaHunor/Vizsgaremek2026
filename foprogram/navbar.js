document.addEventListener("DOMContentLoaded", function () {
  const navbarTarget = document.getElementById("navbar-container");
  if (!navbarTarget) return;

  fetch(getNavbarPath())
    .then(function (response) {
      if (!response.ok) {
        die("Nem sikerült betölteni a navbart.");
      }
      return response.text();
    })
    .then(function (html) {
      navbarTarget.innerHTML = html;

      if (typeof updateMenuAuth === "function") {
        updateMenuAuth();
      }

      if (typeof initThemeToggle === "function") {
        initThemeToggle();
      }
    })
    .catch(function (error) {
      console.error("Navbar betöltési hiba:", error);
    });
});

function getNavbarPath() {
  if (window.location.pathname.includes("/kapcsolat/")) {
    return "../navbar.html";
  }

  return "navbar.html";
}