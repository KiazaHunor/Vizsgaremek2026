document.addEventListener("DOMContentLoaded", async () => {
  const navbarTarget = document.getElementById("footer-container");
  if (!navbarTarget) return;

  try {
    const response = await fetch("footer.html");
    const html = await response.text();
    navbarTarget.innerHTML = html;

    if (typeof updateMenuAuth === "function") {
      updateMenuAuth();
    }

    if (typeof initThemeToggle === "function") {
      initThemeToggle();
    }
  } catch (error) {
    console.error("Footer betöltési hiba:", error);
  }
});