<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>S'inscrire - CookBook</title>
    <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="auth-container">
        <h2>Créer un compte</h2>
        <p class="subtitle">Enregistrez vos idées gourmandes et retrouvez-les partout.</p>
        <div class="form-group">
            <input type="text" id="username" placeholder="Nom d'utilisateur" required>
        </div>
        <div class="form-group">
            <input type="email" id="email" placeholder="Email" required style="width: 100%; padding: 12px 15px; margin-bottom: 1rem; border: 2px solid var(--gray); border-radius: 8px; font-size: 1rem; box-sizing: border-box;">
        </div>
        <div class="form-group">
            <input type="password" id="password" placeholder="Mot de passe" required>
        </div>
        <p id="error-msg"></p>
        <button onclick="signIn()">S'inscrire</button>
        <p>Déjà un compte ? <a href="{{ route('login') }}">Se connecter</a></p>
    </div>


    <script>
        window.ROUTES = {
            home: "{{ route('home') }}",
            login: "{{ route('login') }}",
            apiRegister: "{{ url('/api/register') }}"
        };
    </script>
    <script src="{{ asset('js/signIn.js') }}"></script>
</body>

</html>
