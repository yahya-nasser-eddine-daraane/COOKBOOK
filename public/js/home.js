document.addEventListener('DOMContentLoaded', () => {
    // We rely on Laravel for authentication, but we sync localStorage for UI consistency
    const authLinks = document.querySelector('.auth-links');
    if (authLinks) {
        const logoutBtn = document.getElementById('logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', () => {
                localStorage.removeItem('isLoggedIn');
                localStorage.removeItem('cookbookUser');
                // Laravel handles the actual logout via a form/route
            });
        }
    }

    const grid = document.getElementById('recipes-grid');
    const categoriesList = document.querySelector('.categories-list');

    const staticRecipes = window.aiAssistant.recipes || [];
    const localRecipes = JSON.parse(localStorage.getItem('myRecipes') || '[]');
    const allRecipes = [...localRecipes, ...staticRecipes];

    const categories = [
        { name: 'All', icon: '🍽️' },
        { name: 'Italian', icon: '🍕' },
        { name: 'Asian', icon: '🍜' },
        { name: 'Mexican', icon: '🌮' },
        { name: 'Middle Eastern', icon: '🥙' },
        { name: 'American', icon: '🍔' },
        { name: 'Breakfast', icon: '🥞' },
        { name: 'Dessert', icon: '🍰' },
        { name: 'Healthy', icon: '🥗' },
        { name: 'African', icon: '🥘' },
        { name: 'European', icon: '🥟' },
        { name: 'South American', icon: '🥧' }
    ];

    let currentCategory = 'All';
    function renderCategories() {
        categoriesList.innerHTML = '';
        const fragment = document.createDocumentFragment();

        categories.forEach(cat => {
            const btn = document.createElement('div');
            btn.className = `category-card group ${cat.name === currentCategory ? 'active-category' : ''}`;
            btn.onclick = () => {
                currentCategory = cat.name;
                renderCategories();
                renderRecipes();
            };

            btn.innerHTML = `
                <div class="icon-circle">
                    <span>${cat.icon}</span>
                </div>
                <span class="category-name">${cat.name}</span>
            `;
            fragment.appendChild(btn);
        });

        categoriesList.appendChild(fragment);
    }

    function renderRecipes() {
        grid.innerHTML = '';
        const fragment = document.createDocumentFragment();

        const filtered = currentCategory === 'All'
            ? allRecipes
            : allRecipes.filter(r => r.category === currentCategory);

        filtered.forEach(recipe => {
            const card = document.createElement('a');
            const isLocal = recipe.id !== undefined;
            // In Laravel, we use /recipes/{id}
            const baseUrl = window.ROUTES && window.ROUTES.recipes ? window.ROUTES.recipes : '/recipes';
            card.href = isLocal ? `${baseUrl}/${recipe.id}` : `${baseUrl}?search=${encodeURIComponent(recipe.title)}`;
            card.className = 'recipe-card group';

            const bgStyle = recipe.image ? `background-image: url('${recipe.image}'); background-size: cover; border: none;` : '';

            card.innerHTML = `
                <div class="icon-circle recipe-card-img" style="${bgStyle}">
                    ${!recipe.image ? '<span style="font-size:2rem;">🥘</span>' : ''}
                </div>
                <h3 class="card-title">${recipe.title}</h3>
            `;
            fragment.appendChild(card);
        });

        const addCard = document.createElement('a');
        addCard.href = '/recipes/create';
        addCard.className = 'recipe-card add-recipe-card group';
        addCard.innerHTML = `
            <div class="icon-circle">
                <svg xmlns="http://www.w3.org/2000/svg" class="add-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <h3 class="card-title">Add New Recipe</h3>
        `;
        fragment.appendChild(addCard);

        grid.appendChild(fragment);
    }

    // renderCategories();
    // renderRecipes();

    const surpriseBtn = document.getElementById('surprise-btn');
    if (surpriseBtn) {
        surpriseBtn.addEventListener('click', async () => {
            const originalText = surpriseBtn.textContent;
            surpriseBtn.textContent = 'Finding a surprise... 🎲';
            surpriseBtn.disabled = true;

            try {
                const recipe = await window.aiAssistant.getSurpriseRecipe();
                saveAndRedirect(recipe);
            } catch (error) {
                console.error(error);
                alert('Something went wrong!');
                surpriseBtn.textContent = originalText;
                surpriseBtn.disabled = false;
            }
        });
    }

    function saveAndRedirect(recipe) {
        // Now that the AI controller saves directly to the DB,
        // we just need to redirect to the new recipe page.
        if (recipe && recipe.id) {
            window.location.href = `/recipes/${recipe.id}`;
        } else {
            alert('Failed to create surprise recipe.');
        }
    }
});
