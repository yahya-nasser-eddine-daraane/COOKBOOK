<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Log In - CookBook</title>
    <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="auth-container">
        <h2>Connexion</h2>
        <p class="subtitle">Rejoignez votre carnet de recettes moderne.</p>
        <div class="form-group">
            <input type="text" id="username" placeholder="Nom d'utilisateur" required>
        </div>
        <div class="form-group">
            <input type="password" id="password" placeholder="Mot de passe" required>
        </div>
        <p id="error-msg"></p>
        <button onclick="logIn()">Se connecter</button>
        <p>Pas encore de compte ? <a href="{{ route('register') }}">Inscription</a></p>
    </div>


    <script>
        window.ROUTES = {
            home: "{{ route('home') }}",
            login: "{{ route('login') }}",
            apiLogin: "{{ url('/auth-v1/login') }}"
        };
    </script>
    <script src="{{ asset('js/login.js') }}"></script>
</body>

</html>
