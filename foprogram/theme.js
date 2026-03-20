function initThemeToggle() {
  const toggle = document.getElementById("themeToggle");

  if (!toggle) {
    console.log("Nincs themeToggle");
    return;
  }

  // Előzőleg mentett téma betöltése
  const savedTheme = localStorage.getItem("theme");
  if (savedTheme === "light") {
    document.body.classList.add("light-mode");
    toggle.checked = true;
  } else {
    document.body.classList.remove("light-mode");
    toggle.checked = false;
  }

  // Módosítás a kapcsoló állapotának változásakor
  toggle.addEventListener("change", function () {
    if (this.checked) {
      document.body.classList.add("light-mode");
      localStorage.setItem("theme", "light");
    } else {
      document.body.classList.remove("light-mode");
      localStorage.setItem("theme", "dark");
    }
  });
}

document.addEventListener("DOMContentLoaded", () => {
  const savedTheme = localStorage.getItem("theme");
  if (savedTheme === "light") {
    document.body.classList.add("light-mode");
  }

  initThemeToggle();
});