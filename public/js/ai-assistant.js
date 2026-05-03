window.aiAssistant = {
    generateRecipe: async function(title) {
        try {
            const response = await fetch('/chef-ai/generate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
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
            const response = await fetch('/chef-ai/surprise', {
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
