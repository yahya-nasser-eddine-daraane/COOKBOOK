function logIn() {
    const usernameInput = document.getElementById("username");
    const passwordInput = document.getElementById("password");
    const errorMsg = document.getElementById("error-msg");

    const username = usernameInput.value;
    const password = passwordInput.value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    if (!username || !password) {
        errorMsg.textContent = "Veuillez remplir tous les champs.";
        errorMsg.style.color = "#FF6B6B";
        return;
    }

    fetch(window.ROUTES.apiLogin, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ username, password })
    })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                errorMsg.textContent = "Connexion réussie...";
                errorMsg.style.color = "#4ECDC4";

                // Sync with legacy localStorage auth
                localStorage.setItem('isLoggedIn', 'true');
                localStorage.setItem('cookbookUser', username);

                window.location.href = window.ROUTES.home;
            } else {
                errorMsg.textContent = data.message || "Identifiants incorrects.";
                errorMsg.style.color = "#FF6B6B";
            }
        })
        .catch(error => {
            errorMsg.textContent = error.message || "Erreur de connexion au serveur.";
            errorMsg.style.color = "#FF6B6B";
            console.error('Login Error:', error);
        });
}