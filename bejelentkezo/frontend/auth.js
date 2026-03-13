const API_BASE_URL = 'http://localhost/oliverhtdoc/Vizsgaremek2026/bejelentkezo/backend/api';

function login() {
    const username = document.getElementById('user').value;
    const password = document.getElementById('pass').value;

    if (!username || !password) {
        alert('Töltsd ki mindkét mezőt!');
        return;
    }

    fetch(`${API_BASE_URL}/login.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success && d.token) {
            localStorage.setItem('token', d.token);
            window.location.href = '../../foprogram/szep.html';
        } else {
            alert(d.error || 'Hibás adatok');
        }
    })
    .catch(error => {
        alert('Hiba történt');
        console.error(error);
    });
}

function register() {
    const username = document.getElementById('user').value;
    const password = document.getElementById('pass').value;
    const password_conf = document.getElementById('passconf').value;
    const email = document.getElementById('email').value;

    if (!username || !password || !password_conf || !email) {
        alert('Minden mezőt tölts ki!');
        return;
    }

    if (password.length < 6) {
        alert('A jelszónak legalább 6 karakteresnek kell lennie');
        return;
    }
    if (password !== password_conf) {
        alert('A jelszók nem egyeznek meg egymással!');
        return;
    }
    if (!email.includes('@')) {
        alert('Nem jó az email cím!');
        return;
    }

    fetch(`${API_BASE_URL}/register.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password, password_conf, email })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            alert(d.message || 'Sikeres regisztráció');
            document.getElementById('user').value = '';
            document.getElementById('pass').value = '';
            document.getElementById('passconf').value = '';
            document.getElementById('email').value = '';
            window.location.href = 'index.html';
        } else {
            alert(d.error || 'Sikertelen regisztráció');
        }
    })
    .catch(error => {
        alert('Hiba történt');
        console.error(error);
    });
}

function requestPasswordReset() {
    const email = document.getElementById('email').value.trim();

    if (!email) {
        alert('Add meg az email címedet!');
        return;
    }

    fetch(`${API_BASE_URL}/newpass.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'request', email })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            alert(d.message || 'Ha az email létezik, küldtünk egy linket.');
            document.getElementById('email').value = '';
        } else {
            alert(d.error || 'Nem sikerült az email küldése');
        }
    })
    .catch(error => {
        alert('Hiba történt a kérés során');
        console.error(error);
    });
}


console.log(`${API_BASE_URL}/newpass.php`);

function resetPassword() {
    const token = document.getElementById('resetToken').value.trim();
    const password = document.getElementById('pass').value;
    const password_conf = document.getElementById('passconf').value;

    if (!token || !password || !password_conf) {
        alert('Minden mezőt tölts ki!');
        return;
    }

    if (password.length < 6) {
        alert('A jelszónak legalább 6 karakteresnek kell lennie');
        return;
    }

    if (password !== password_conf) {
        alert('A két jelszó nem egyezik');
        return;
    }

    fetch(`${API_BASE_URL}/newpass.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'reset', token, password, password_conf })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            alert(d.message || 'Sikeres jelszócsere');
            window.location.href = 'index.html';
        } else {
            alert(d.error || 'Sikertelen jelszócsere');
        }
    })
    .catch(error => {
        alert('Hiba történt a jelszó frissítésekor');
        console.error(error);
    });
}
function fillResetTokenFromUrl() {
    const tokenField = document.getElementById('resetToken');
    if (!tokenField) return;

    const params = new URLSearchParams(window.location.search);
    const token = params.get('token');

    if (!token) {
        alert('Hiányzó vagy érvénytelen jelszó-visszaállító token.');
        window.location.href = 'index.html';
        return;
    }

    tokenField.value = token;
    console.log('Token betöltve:', token);
}