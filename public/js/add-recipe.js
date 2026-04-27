function createIngredientInput() {
    const div = document.createElement('div');
    div.className = 'dynamic-item';

    div.innerHTML = `
        <div class="ingredient-row-main">
            <input type="text" name="ingredient_names[]" placeholder="Ingredient name (ex: Flour)" class="form-input ingredient-name" required>
            <input type="text" name="quantities[]" placeholder="Amount (ex: 500g)" class="form-input ingredient-amount" required>
            <button type="button" class="btn-remove" onclick="this.parentElement.remove()">X</button>
        </div>
        <div class="ingredient-row-image">
            <i class="fas fa-image"></i>
            <input type="text" name="ingredient_links[]" placeholder="Photo link (optional)" class="form-input ingredient-link">
        </div>
    `;
    return div;
}

function selectIngredient(select) {
    // No longer used with text inputs
}

function createInstructionInput() {
    const div = document.createElement('div');
    div.className = 'dynamic-item';
    div.innerHTML = `
        <textarea placeholder="Step description..." class="form-input instruction-text" required></textarea>
        <button type="button" class="btn-remove" onclick="this.parentElement.remove()">X</button>
    `;
    return div;
}

document.addEventListener('DOMContentLoaded', () => {
    const ingredientsList = document.getElementById('ingredients-list');
    const instructionsList = document.getElementById('instructions-list');

    // Add initial rows
    ingredientsList.appendChild(createIngredientInput());
    instructionsList.appendChild(createInstructionInput());

    document.getElementById('add-ingredient-btn').addEventListener('click', () => {
        ingredientsList.appendChild(createIngredientInput());
    });

    document.getElementById('add-instruction-btn').addEventListener('click', () => {
        instructionsList.appendChild(createInstructionInput());
    });

    // AI Suggestion Logic
    const aiSuggestBtn = document.getElementById('ai-suggest-btn');
    if (aiSuggestBtn) {
        aiSuggestBtn.addEventListener('click', async () => {
            const titleInput = document.getElementById('recipe-title');
            const title = titleInput.value.trim();

            if (!title) {
                alert('Please enter a recipe title first (ex: "Chocolate Cake")');
                return;
            }

            const originalText = aiSuggestBtn.textContent;
            aiSuggestBtn.textContent = 'Generating... 🤖';
            aiSuggestBtn.disabled = true;

            try {
                const generatedRecipe = await window.aiAssistant.generateRecipe(title);
                document.getElementById('recipe-time').value = generatedRecipe.meta.time;
                document.getElementById('recipe-servings').value = generatedRecipe.meta.servings;
                document.getElementById('recipe-calories').value = generatedRecipe.meta.calories;

                const imgInput = document.getElementById('recipe-image');
                if (!imgInput.value && generatedRecipe.image) {
                    imgInput.value = generatedRecipe.image;
                }

                // Populate Ingredients
                const ingredientsList = document.getElementById('ingredients-list');
                ingredientsList.innerHTML = '';
                generatedRecipe.ingredients.forEach(ing => {
                    const row = createIngredientInput();
                    row.querySelector('.ingredient-name').value = ing.name;
                    row.querySelector('.ingredient-amount').value = ing.amount;
                    
                    // Add AI image if available or generate a high-quality food link
                    const linkInput = row.querySelector('.ingredient-link');
                    if (ing.image) {
                        linkInput.value = ing.image;
                    } else {
                        linkInput.value = `https://placehold.co/100x100/E8F5E9/2E7D32?text=${ing.name.charAt(0)}`;
                    }
                    
                    ingredientsList.appendChild(row);
                });

                // Populate Instructions
                const instructionsList = document.getElementById('instructions-list');
                instructionsList.innerHTML = '';
                generatedRecipe.instructions.forEach(step => {
                    const row = createInstructionInput();
                    row.querySelector('.instruction-text').value = step;
                    instructionsList.appendChild(row);
                });

                alert('Recipe generated! Please review ingredients before saving.');

            } catch (error) {
                alert('Failed to generate: ' + error.message);
            } finally {
                aiSuggestBtn.textContent = originalText;
                aiSuggestBtn.disabled = false;
            }
        });
    }

    // Form Submit Handler
    document.getElementById('add-recipe-form').addEventListener('submit', (e) => {
        // We do NOT preventDefault(); we let the form submit to Laravel.

        // Aggregate instructions into hidden input
        const instructions = Array.from(document.querySelectorAll('.instruction-text'))
            .map(el => el.value)
            .join('\n');

        document.getElementById('instructions-hidden').value = instructions;
    });
});
