function signIn() {
    const usernameInput = document.getElementById("username");
    const emailInput = document.getElementById("email");
    const passwordInput = document.getElementById("password");
    const errorMsg = document.getElementById("error-msg");

    const username = usernameInput.value;
    const email = emailInput.value;
    const password = passwordInput.value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    if (!username || !email || !password) {
        errorMsg.textContent = "Veuillez remplir tous les champs.";
        errorMsg.style.color = "#FF6B6B";
        return;
    }

    fetch(window.ROUTES.apiRegister, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ username, email, password })
    })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                errorMsg.textContent = "Compte créé ! Redirection...";
                errorMsg.style.color = "#4ECDC4";

                // Sync with legacy localStorage auth
                localStorage.setItem('isLoggedIn', 'true');
                localStorage.setItem('cookbookUser', username);

                setTimeout(() => {
                    window.location.href = window.ROUTES.home;
                }, 1500);
            } else {
                errorMsg.textContent = data.message || "Erreur lors de l'inscription.";
                errorMsg.style.color = "#FF6B6B";
            }
        })
        .catch(error => {
            errorMsg.textContent = error.message || "Erreur de connexion au serveur.";
            errorMsg.style.color = "#FF6B6B";
            console.error('Registration Error:', error);
        });
}