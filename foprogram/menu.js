function updateMenuAuth() {
  const token = localStorage.getItem("token");
  const bejelLink = document.getElementById("bejel");

  if (!bejelLink) return;

  if (token) {
    fetch("http://localhost/oliverhtdoc/Vizsgaremek2026/bejelentkezo/backend/api/profile.php", {
      headers: {
        Authorization: "Bearer " + token
      }
    })
    .then(r => r.json())
    .then(d => {
      if (!d.success) {
        localStorage.removeItem("token");
        location.reload();
        return;
      }

      const user = d.user;

      const imageUrl = user.profile_image
        ? "http://localhost/oliverhtdoc/Vizsgaremek2026/bejelentkezo/backend/" + user.profile_image
        : "https://via.placeholder.com/40";

      bejelLink.href = "../bejelentkezo/frontend/dashboard.html";

      bejelLink.innerHTML = `
        <img src="${imageUrl}" 
             style="width:30px;height:30px;border-radius:50%;object-fit:cover;margin-right:6px;">
        ${user.username}
      `;

      bejelLink.classList.remove("text-warning");
      bejelLink.classList.add("text-success");
    })
    .catch(() => {
      localStorage.removeItem("token");
      location.reload();
    });

  } else {
    bejelLink.href = "../bejelentkezo/frontend/index.html";
    bejelLink.textContent = "👤 Bejelentkezés / Regisztráció";
    bejelLink.classList.remove("text-success");
    bejelLink.classList.add("text-warning");
  }
}