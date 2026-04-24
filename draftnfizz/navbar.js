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

function loadCredits() {
  const token = localStorage.getItem("token");

  if (!token) return;

  fetch("get_user_credits.php", {
    headers: {
      Authorization: "Bearer " + token
    }
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      const el = document.getElementById("credit-display");
      if (el) {
        el.textContent = "💰 " + data.credits;
      }
    }
  });
}
fetch("navbar.html")
  .then(res => res.text())
  .then(data => {
    document.getElementById("navbar-container").innerHTML = data;

    // 👇 EZ A LÉNYEG
    loadCredits();
  });
document.addEventListener("DOMContentLoaded", loadCredits);