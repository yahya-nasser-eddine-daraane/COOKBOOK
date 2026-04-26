window.aiAssistant = {
    generateRecipe: async function(title) {
        try {
            const response = await fetch('/api/ai/generate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ title: title })
            });

            if (!response.ok) throw new Error('Generation failed');
            return await response.json();
        } catch (error) {
            console.error('Error generating recipe:', error);
            throw error;
        }
    },

    getSurpriseRecipe: async function() {
        try {
            const response = await fetch('/api/ai/surprise', {
                method: 'GET',
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) throw new Error('Surprise failed');
            return await response.json();
        } catch (error) {
            console.error('Error getting surprise recipe:', error);
            throw error;
        }
    }
};
