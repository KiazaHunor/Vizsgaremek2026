document.addEventListener("DOMContentLoaded", async () => {
  const navbarTarget = document.getElementById("navbar-container");
  if (!navbarTarget) return;

  try {
    const response = await fetch("navbar.html");
    const html = await response.text();
    navbarTarget.innerHTML = html;

    if (typeof updateMenuAuth === "function") {
      updateMenuAuth();
    }

    if (typeof initThemeToggle === "function") {
      initThemeToggle();
    }
  } catch (error) {
    console.error("Navbar betöltési hiba:", error);
  }
});