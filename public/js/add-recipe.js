function createIngredientInput() {
    const div = document.createElement('div');
    div.className = 'dynamic-item';

    let optionsHTML = '<option value=""> Select Ingredient </option>';
    const ingredients = (typeof SERVER_INGREDIENTS !== 'undefined') ? SERVER_INGREDIENTS : [];

    ingredients.forEach(ing => {
        optionsHTML += `<option value="${ing.id}" data-image="${ing.image_path || ''}">${ing.name}</option>`;
    });

    div.innerHTML = `
        <select name="ingredients[]" class="form-input ingredient-select" required onchange="selectIngredient(this)">
            ${optionsHTML}
        </select> 
        <input type="text" name="quantities[]" placeholder="Amount (ex:, 500g)" class="form-input ingredient-amount" required>
        <input type="hidden" class="ingredient-img">
        <button type="button" class="btn-remove" onclick="this.parentElement.remove()">X</button>
    `;
    return div;
}

function selectIngredient(select) {
    const selectedOption = select.options[select.selectedIndex];
    const imageUrl = selectedOption.getAttribute('data-image');
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

    // AI Suggestion Logic (Preserved but slightly adapted)
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

                // Image handling: generatedRecipe.image is a URL
                const imgInput = document.getElementById('recipe-image');
                if (!imgInput.value && generatedRecipe.image) {
                    imgInput.value = generatedRecipe.image;
                }

                // Populate Ingredients
                const ingredientsList = document.getElementById('ingredients-list');
                ingredientsList.innerHTML = '';
                generatedRecipe.ingredients.forEach(ing => {
                    const row = createIngredientInput();
                    const select = row.querySelector('.ingredient-select');
                    const amount = row.querySelector('.ingredient-amount');

                    let found = false;
                    for (let i = 0; i < select.options.length; i++) {
                        // Match by text name
                        if (select.options[i].text.toLowerCase() === ing.name.toLowerCase()) {
                            select.selectedIndex = i;
                            found = true;
                            break;
                        }
                    }

                    if (!found) {
                        console.warn('Ingredient not found in DB:', ing.name);
                    }

                    amount.value = ing.amount;
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
