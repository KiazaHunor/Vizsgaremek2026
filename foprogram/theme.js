function initThemeToggle() {
  const toggle = document.getElementById("themeToggle");
  if (!toggle) {
    console.log("Nincs themeToggle");
    return;
  }

  const savedTheme = localStorage.getItem("theme");

  if (savedTheme === "light") {
    document.body.classList.add("light-mode");
    toggle.checked = true;
  } else {
    document.body.classList.remove("light-mode");
    toggle.checked = false;
  }

  toggle.addEventListener("change", function () {
    console.log("Kattintás működik", this.checked);

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
  } else {
    document.body.classList.remove("light-mode");
  }
});