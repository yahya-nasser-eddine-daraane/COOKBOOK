<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Recipe - CookBook</title>
    <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/add-recipe.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
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
                    <button id="surprise-btn" class="btn btn-warning btn-lg">
                Surprise recipe
            </button>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container">
        <div class="form-container">
            <h2 class="section-title text-center">Add New Recipe</h2>

            <form id="add-recipe-form" method="POST" action="{{ route('recipes.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="form-label">Recipe Title</label>
                    <div class="input-row">
                        <input type="text" id="recipe-title" name="title" class="form-input" required
                            placeholder="ex: Homemade Pizza">
                        <button type="button" id="ai-suggest-btn" class="btn btn-warning no-wrap">
                            AI Suggestions</button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-input" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Description (Optional)</label>
                    <textarea name="description" class="form-input" rows="3" placeholder="Briefly describe your recipe..."></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Recipe Image</label>
                    
                    <div class="image-options-grid">
                        <!-- Option 1: File Upload -->
                        <div class="image-option-card">
                            <div class="option-header">
                                <i class="fas fa-camera"></i>
                                <span>Upload Photo / Take Picture</span>
                            </div>
                            <div class="file-upload-wrapper">
                                <input type="file" name="image_file" id="image-file-input" class="file-upload-input" accept="image/*" capture="environment">
                                <div class="file-upload-design">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>Select File</p>
                                    <span id="file-name-display" class="file-name-hint">No file chosen</span>
                                </div>
                            </div>
                        </div>

                        <!-- Option 2: AI / Web Link -->
                        <div class="image-option-card">
                            <div class="option-header">
                                <i class="fas fa-link"></i>
                                <span>Link</span>
                            </div>
                            <div class="input-with-icon">
                                <i class="fas fa-magic"></i>
                                <input type="text" id="recipe-image" name="image_path" class="form-input" 
                                    placeholder="Paste image URL here...">
                            </div>
                            <p class="input-hint">This will be automatically filled if you click "AI Suggestions" above.</p>
                        </div>
                    </div>
                </div>

                <style>
                    .image-options-grid {
                        display: grid;
                        grid-template-columns: 1fr 1fr;
                        gap: 20px;
                        background: rgba(255, 255, 255, 0.05);
                        border-radius: 15px;
                        padding: 15px;
                        border: 1px solid rgba(0,0,0,0.1);
                    }
                    @media (max-width: 768px) {
                        .image-options-grid { grid-template-columns: 1fr; }
                    }
                    .image-option-card {
                        background: white;
                        padding: 15px;
                        border-radius: 12px;
                        border: 1px solid #eee;
                        display: flex;
                        flex-direction: column;
                        gap: 12px;
                    }
                    .option-header {
                        display: flex;
                        align-items: center;
                        gap: 10px;
                        font-weight: 600;
                        color: #333;
                        font-size: 0.95rem;
                    }
                    .option-header i { color: var(--primary-color); }
                    
                    /* File Upload Design */
                    .file-upload-wrapper { position: relative; width: 100%; height: 80px; }
                    .file-upload-input { position: absolute; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 2; }
                    .file-upload-design {
                        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
                        border: 2px dashed #ddd; border-radius: 10px;
                        display: flex; align-items: center; justify-content: center; gap: 10px;
                        background: #f9f9f9; transition: all 0.3s ease;
                    }
                    .file-upload-wrapper:hover .file-upload-design { border-color: var(--primary-color); }
                    .file-name-hint { font-size: 0.8rem; color: #888; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px; }

                    /* Link Input */
                    .input-with-icon { position: relative; }
                    .input-with-icon i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--primary-color); }
                    .input-with-icon .form-input { padding-left: 35px; height: 45px; }
                    .input-hint { font-size: 0.75rem; color: #888; margin-top: 0; font-style: italic; }
                </style>

                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const fileInput = document.getElementById('image-file-input');
                        const fileNameDisplay = document.getElementById('file-name-display');
                        if (fileInput) {
                            fileInput.addEventListener('change', (e) => {
                                if (e.target.files.length > 0) {
                                    fileNameDisplay.textContent = e.target.files[0].name;
                                    fileNameDisplay.style.color = 'var(--primary-color)';
                                }
                            });
                        }
                    });
                </script>

                <div class="form-row form-group">
                    <div>
                        <label class="form-label">Time (mins)</label>
                        <input type="number" id="recipe-time" name="prep_time" class="form-input" required placeholder="0">
                    </div>
                    <div>
                        <label class="form-label">Servings</label>
                        <input type="number" id="recipe-servings" name="servings" class="form-input" required placeholder="0">
                    </div>
                    <div>
                        <label class="form-label">Calories</label>
                        <input type="number" id="recipe-calories" name="calories" class="form-input" required placeholder="0">
                    </div>
                </div>

                <hr class="divider">

                <div class="form-group">
                    <label class="form-label">Ingredients</label>
                    <div id="ingredients-list" class="dynamic-list"></div>
                    <button type="button" id="add-ingredient-btn" class="btn btn-primary btn-small mt-1">
                        + Add Ingredient</button>
                </div>

                <div class="form-group">
                    <label class="form-label">Instructions</label>
                    <div id="instructions-list" class="dynamic-list">

                    </div>
                    <button type="button" id="add-instruction-btn" class="btn btn-primary btn-small mt-1">
                        + Add Step</button>
                </div>

                <!-- Hidden input to aggregate instructions -->
                <input type="hidden" name="instructions" id="instructions-hidden">

                <button type="submit" class="btn btn-warning w-100 mt-2">
                    Save Recipe
                </button>
            </form>
        </div>
    </main>



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
    <script src="{{ asset('js/ingredients-db.js') }}"></script>
    <script src="{{ asset('js/ai-assistant.js') }}"></script>
    <script src="{{ asset('js/add-recipe.js') }}"></script>
</body>

</html>
