function updateMenuAuth() {
  const token = localStorage.getItem('token');
  const bejelLink = document.getElementById('bejel');

  if (!bejelLink) return;

  if (token) {
    bejelLink.href = '../bejelentkezo/frontend/dashboard.html';
    bejelLink.textContent = '👤 Profil';
    bejelLink.classList.remove('text-warning');
    bejelLink.classList.add('text-success');
  } else {
    bejelLink.href = '../bejelentkezo/frontend/index.html';
    bejelLink.textContent = '👤 Bejelentkezés / Regisztráció';
    bejelLink.classList.remove('text-success');
    bejelLink.classList.add('text-warning');
  }
}

document.addEventListener('DOMContentLoaded', updateMenuAuth);