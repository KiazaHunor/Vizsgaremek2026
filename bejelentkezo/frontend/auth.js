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
        body: JSON.stringify({ username: username, password: password })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success && d.token) {
            localStorage.setItem('token', d.token);
            window.location.href = '../../szep.html';
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
    const password_conf=document.getElementById("passconf").value;
    const email=document.getElementById("email").value;

    if (!username || !password) {
        alert('Töltsd ki mindkét mezőt!');
        return;
    }
    
    if (password.length < 6) {
        alert('A jelszónak legalább 6 karakteresnek kell lennie');
        return;
    }
    if(password!=password_conf){
        alert("A jelszók nem egyeznek meg egymással!");
        return;
    }
    if(!email.includes("@")){
        alert("Nem jó az email cím!");
        return;
    }
    
    fetch(`${API_BASE_URL}/register.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username: username, password: password, password_conf:password_conf,email:email })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            alert('Sikeres regisztráció');
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