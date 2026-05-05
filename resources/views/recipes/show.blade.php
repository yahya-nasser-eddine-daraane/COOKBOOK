<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $recipe->title }} - CookBook</title>
    <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/recipe.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <!-- Navbar (Same as Home)-->
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

                <div class="nav-auth-mobile">
                    @auth
                        <span style="font-weight:600; color: var(--dark);"><i class="fas fa-user-circle"></i> {{ Auth::user()->name }}</span>
                        <a href="{{ route('recipes.my') }}" class="btn btn-ghost" style="text-align:center;"><i class="fas fa-utensils"></i> Mes Recettes</a>
                        <a href="#" class="btn btn-primary" onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();"><i class="fas fa-sign-out-alt"></i> Se déconnecter</a>
                        <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-ghost">Se connecter</a>
                        <a href="{{ route('register') }}" class="btn btn-primary">S'inscrire</a>
                    @endauth
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

    <!-- Hero Section -->
    <header class="recipe-hero" id="recipe-hero" style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('{{ $recipe->image_path ? (Str::startsWith($recipe->image_path, ['http://', 'https://']) ? $recipe->image_path : asset($recipe->image_path)) : $recipe->fallback_image }}'); background-size: cover; background-position: center;">
        <script>
            // CSS background-image doesn't have an onerror fallback.
            // This tiny script tests the image and swaps the background if it fails to load.
            (function() {
                var heroUrl = "{{ $recipe->image_path ? (Str::startsWith($recipe->image_path, ['http://', 'https://']) ? $recipe->image_path : asset($recipe->image_path)) : '' }}";
                var fallbackUrl = "{{ $recipe->fallback_image }}";
                var ultimateFallback = "https://placehold.co/1200x600/FFF3E0/E65100?text=Recipe";
                
                if (heroUrl) {
                    var img = new Image();
                    img.onerror = function() {
                        // The primary image failed. Try the category fallback.
                        document.getElementById('recipe-hero').style.backgroundImage = "linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('" + fallbackUrl + "')";
                        
                        // Also test if the category fallback fails
                        var fbImg = new Image();
                        fbImg.onerror = function() {
                            document.getElementById('recipe-hero').style.backgroundImage = "linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('" + ultimateFallback + "')";
                        };
                        fbImg.src = fallbackUrl;
                    };
                    img.src = heroUrl;
                } else {
                    // No hero url provided, we are currently using fallbackUrl. Test if it fails.
                    var fbImg = new Image();
                    fbImg.onerror = function() {
                        document.getElementById('recipe-hero').style.backgroundImage = "linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('" + ultimateFallback + "')";
                    };
                    fbImg.src = fallbackUrl;
                }
            })();
        </script>
        <div class="container recipe-hero-content">
            <h1 class="recipe-title">{{ $recipe->title }}</h1>
            <div class="recipe-meta">
                <div class="meta-item">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ $recipe->prep_time }} Min</span>
                </div>
                <div class="meta-item">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>{{ $recipe->servings }} Servings</span>
                </div>
                <div class="meta-item">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>{{ $recipe->calories }} Kcal</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-user-circle"></i>
                    <span>Chef: {{ $recipe->user->name ?? 'Admin' }}</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-users"></i>
                    <span>{{ $recipe->viewers_count }} Visitors</span>
                </div>
            </div>
        </div>
    </header>

    <main class="container">

        <!-- Ingredients Section -->
        <section class="section ingredients-section">
            <h2 class="section-title">Ingredients</h2>
            <div class="ingredients-grid">
                @foreach($recipe->ingredients as $ingredient)
                <div class="ingredient-card">
                    <div class="ingredient-img-container">
                        <img src="{{ $ingredient->image_path ? (Str::startsWith($ingredient->image_path, ['http://', 'https://']) ? $ingredient->image_path : asset($ingredient->image_path)) : 'https://img.spoonacular.com/ingredients_100x100/'.urlencode(strtolower(str_replace(' ', '-', $ingredient->name))).'.jpg' }}" 
                             onerror="this.onerror=null; this.src='https://placehold.co/100x100/E8F5E9/2E7D32?text={{ urlencode(substr($ingredient->name, 0, 1)) }}'"
                             alt="{{ $ingredient->name }}" class="ingredient-img">
                    </div>
                    <div class="ingredient-info">
                        <span class="ingredient-name">{{ $ingredient->name }}</span>
                        <span class="ingredient-amount">{{ $ingredient->pivot->quantity }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        <!-- Instructions Section -->
        <section class="section instructions-section">
            <h2 class="section-title">Instructions</h2>

            <div class="timer-container">
                <div class="base-timer">
                    <svg class="base-timer__svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                        <g class="base-timer__circle">
                            <circle class="base-timer__path-elapsed" cx="50" cy="50" r="45"></circle>
                            <path id="base-timer-path-remaining" stroke-dasharray="283"
                                class="base-timer__path-remaining" d="
                                M 50, 50
                                m -45, 0
                                a 45,45 0 1,0 90,0
                                a 45,45 0 1,0 -90,0
                                ">
                            </path>
                        </g>
                    </svg>
                    <span id="base-timer-label" class="base-timer__label">{{ $recipe->prep_time }}:00</span>
                </div>
                <button id="start-timer-btn" class="btn btn-warning">Start Timer</button>
            </div>

            <ol class="instructions-list">
                @foreach(explode("\n", $recipe->instructions) as $step)
                    @if(trim($step))
                    <li class="instruction-step">
                        <div class="step-text">
                            {{ $step }}
                        </div>
                    </li>
                    @endif
                @endforeach
            </ol>
        </section>

    </main>

    <script>
        window.recipeTime = {{ $recipe->prep_time }};
    </script>
    
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
    <script src="{{ asset('js/ai-assistant.js') }}"></script>
    <script src="{{ asset('js/recipe.js') }}"></script>
</body>

</html>
