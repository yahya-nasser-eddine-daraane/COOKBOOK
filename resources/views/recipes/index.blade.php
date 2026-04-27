<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Recipes - CookBook</title>
    <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <style>
        .text-center { text-align: center; }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <nav class="navbar">
        <div class="container nav-container">
            <a href="{{ route('home') }}" class="logo-link">
                <span class="logo-text">COOKBOOK</span>
            </a>

            <button class="hamburger" id="nav-toggle">
                <i class="fas fa-bars"></i>
            </button>

            <div class="nav-main" id="nav-menu">
                <ul class="nav-links">
                    <li><a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Accueil</a></li>
                    @auth
                        <li><a href="{{ route('recipes.my') }}" class="nav-link {{ request()->routeIs('recipes.my') ? 'active' : '' }}">Mes Recettes</a></li>
                    @else
                        <li><a href="{{ route('recipes.index') }}" class="nav-link {{ request()->routeIs('recipes.index') ? 'active' : '' }}">Recettes</a></li>
                    @endauth
                    <li><a href="{{ route('recipes.create') }}" class="nav-link {{ request()->routeIs('recipes.create') ? 'active' : '' }}">Publier</a></li>
                </ul>

                <div class="search-container">
                    <form action="{{ route('recipes.index') }}" method="GET">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" name="search" class="search-input" placeholder="Rechercher une recette..." value="{{ request('search') }}">
                    </form>
                </div>
            </div>

            <div class="auth-links">
                @auth
                    <div class="user-menu">
                        <button class="btn btn-ghost user-btn">
                            <i class="fas fa-user-circle"></i> {{ Auth::user()->name }} <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="user-dropdown">
                            <a href="{{ route('recipes.my') }}"><i class="fas fa-utensils"></i> Mes Recettes</a>
                            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i> Se déconnecter
                            </a>
                        </div>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-ghost">Se connecter</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">S'inscrire</a>
                @endauth
            </div>
        </div>
    </nav>

    <section class="section-container recipes-section">
        <h2 class="section-title text-center">{{ $title ?? 'All Recipes' }}</h2>
        <div class="grid container" id="recipes-grid">
            @foreach($recipes as $recipe)
                <div class="recipe-card group">
                    <img src="{{ $recipe->image_path ? (Str::startsWith($recipe->image_path, ['http://', 'https://']) ? $recipe->image_path : asset($recipe->image_path)) : $recipe->fallback_image }}" 
                         onerror="if (this.src !== '{{ $recipe->fallback_image }}') { this.src='{{ $recipe->fallback_image }}'; } else { this.onerror=null; this.src='https://placehold.co/600x400/FFF3E0/E65100?text=Recipe'; }"
                         alt="{{ $recipe->title }}" class="recipe-img">
                    <div class="recipe-content">
                        <h3 class="card-title">{{ $recipe->title }}</h3>
                        <div class="recipe-meta-row">
                             <span><i class="fas fa-clock"></i> {{ $recipe->prep_time }} min</span>
                             <span><i class="fas fa-fire"></i> {{ $recipe->calories }} kcal</span>
                        </div>
                        <div class="recipe-meta-row author-row">
                             <span><i class="fas fa-user"></i> Chef: {{ $recipe->user->name ?? 'Admin' }}</span>
                             <span><i class="fas fa-users"></i> {{ $recipe->viewers_count }}</span>
                        </div>
                        <a href="{{ route('recipes.show', $recipe->id) }}" class="view-recipe-link">View Recipe</a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <footer class="footer">
        <div class="container footer-container">
            <div class="footer-logo">
                <span class="logo-text">COOKBOOK</span>
            </div>
            <div class="footer-socials">
                <a href="https://www.instagram.com/yahya_nasser_eddine?igsh=dDJ4dHk3anFnYzB1" target="_blank"
                    rel="noopener noreferrer" class="social-circle" aria-label="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://github.com/yahya-nasser-eddine-daraane" target="_blank" rel="noopener noreferrer"
                    class="social-circle" aria-label="GitHub">
                    <i class="fab fa-github"></i>
                </a>
                <a href="mailto:yahyadaraan@gmail.com" class="social-circle" aria-label="Email">
                    <i class="fas fa-envelope"></i>
                </a>
            </div>
        </div>
    </footer>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggle = document.getElementById('nav-toggle');
            const menu = document.getElementById('nav-menu');
            if (toggle && menu) {
                toggle.addEventListener('click', () => {
                    menu.classList.toggle('active');
                });
            }
        });
    </script>
</body>

</html>
