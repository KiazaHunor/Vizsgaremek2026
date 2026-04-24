function updateMenuAuth() {
  const token = localStorage.getItem("token");
  const bejelLink = document.getElementById("bejel");

  if (!bejelLink) return;

  if (!token) {
    bejelLink.href = "bejelentkezo/frontend/index.html";
    bejelLink.textContent = "👤 Bejelentkezés / Regisztráció";
    bejelLink.classList.remove("text-success");
    bejelLink.classList.add("text-warning");
    return;
  }

  fetch('bejelentkezo/backend/api/profile.php', {
  headers: {
    Authorization: 'Bearer ' + localStorage.getItem('token')
  }
}) .then(async (r) => {
      const data = await r.json();
      if (!r.ok || !data.success) {
        throw new Error(data.error || "Érvénytelen token");
      }
      return data;
    })
    .then((d) => {
      const user = d.user;

      bejelLink.href = "bejelentkezo/frontend/dashboard.html";

      if (user.profile_image) {
        const imageUrl = "bejelentkezo/backend/" + user.profile_image;
        bejelLink.innerHTML = `
          <img src="${imageUrl}"
               style="width:30px;height:30px;border-radius:50%;object-fit:cover;margin-right:6px;">
          ${user.username}
        `;
      } else {
        bejelLink.textContent = "👤 " + user.username;
      }

      bejelLink.classList.remove("text-warning");
      bejelLink.classList.add("text-success");
    })
    .catch((error) => {
      console.error("Profil lekérési hiba:", error);

      bejelLink.href = "bejelentkezo/frontend/index.html";
      bejelLink.textContent = "👤 Bejelentkezés / Regisztráció";
      bejelLink.classList.remove("text-success");
      bejelLink.classList.add("text-warning");
    });
}