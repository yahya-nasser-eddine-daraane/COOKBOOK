<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Recipe;
use App\Models\Category;
use App\Models\Ingredient;

class AiController extends Controller
{
    private $apiKey;
    private $baseUrl = 'https://api.groq.com/openai/v1/chat/completions';
    private $model = 'llama-3.3-70b-versatile';

    public function __construct()
    {
        $this->apiKey = env('GROQ_API_KEY');
    }

    public function generateRecipe(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255'
        ]);

        $title = $request->input('title');

        $prompt = "You are an expert chef assistant. Generate a delicious recipe for '{$title}'.
        Return ONLY a JSON object (no markdown, no other text) with the following exact structure:
        {
            \"meta\": {
                \"time\": 45,
                \"servings\": 4,
                \"calories\": 350
            },
            \"image\": \"A relevant high-quality food image URL\",
            \"ingredients\": [
                {
                    \"name\": \"Ingredient Name\",
                    \"amount\": \"Quantity (e.g. 2 cups)\"
                }
            ],
            \"instructions\": [
                \"Rinse the quinoa.\",
                \"Cook the quinoa in water for 15 minutes.\",
                \"Fluff the quinoa and let it cool.\"
            ]
        }
        IMPORTANT: Provide a COMPLETE recipe. Do not stop at 5 steps. If the recipe needs 10, 15, or 20 steps to be clear, provide ALL of them. Each string in the 'instructions' array MUST contain ONLY ONE single action.";

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->post($this->baseUrl, [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a helpful AI chef. Always return strict JSON.'],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'response_format' => ['type' => 'json_object'],
                    'temperature' => 0.7
                ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                return response()->json(json_decode($content, true));
            }

            Log::error('Groq API Error', ['status' => $response->status(), 'body' => $response->body()]);
            return response()->json(['error' => 'AI Generation failed.'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function surpriseRecipe()
    {
        $prompt = "Generate a TRULY RANDOM and creative recipe. Pick a random cuisine (Italian, Asian, African, Mexican, etc.), a random main ingredient (Fish, Tofu, Lamb, exotic fruits, etc.), and a random cooking style. 
        Return ONLY a JSON object with this structure:
        {
            \"id\": \"surprise_rand\",
            \"title\": \"Title\",
            \"category\": \"Dinner\",
            \"image\": \"A high-quality Unsplash URL for this specific dish\",
            \"meta\": {\"time\": 30, \"servings\": 2, \"calories\": 400},
            \"ingredients\": [{\"name\": \"Ing\", \"amount\": \"Amt\", \"image\": \"A high-quality Unsplash URL for this specific ingredient\"}],
            \"instructions\": [\"Action 1\", \"Action 2\"]
        }
        IMPORTANT: Provide a COMPLETE recipe. Do not stop at 5 steps. If the recipe needs 10, 15, or 20 steps to be clear, provide ALL of them. Each string in the 'instructions' array MUST contain ONLY ONE single action.";

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->post($this->baseUrl, [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a helpful AI chef. Always return strict JSON.'],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'response_format' => ['type' => 'json_object'],
                    'temperature' => 1.0
                ]);

            if ($response->successful()) {
                $recipeData = json_decode($response->json('choices.0.message.content'), true);
                
                // --- Save logic ---
                $categoryName = $recipeData['category'] ?? 'Healthy';
                $category = Category::where('name', 'like', "%{$categoryName}%")->first();
                if (!$category) {
                    $category = Category::inRandomOrder()->first() ?? Category::first();
                }
                
                $recipe = new Recipe();
                $recipe->title = $recipeData['title'];
                $recipe->description = "An AI generated surprise recipe! Enjoy this creative " . ($recipeData['category'] ?? 'dish') . ".";
                $recipe->instructions = implode("\n", $recipeData['instructions']);
                $recipe->prep_time = $recipeData['meta']['time'] ?? 30;
                $recipe->servings = $recipeData['meta']['servings'] ?? 2;
                $recipe->calories = $recipeData['meta']['calories'] ?? 400;
                $recipe->image_path = $recipeData['image'];
                $recipe->category_id = $category->id;
                $recipe->user_id = Auth::id() ?? \App\Models\User::first()->id;
                $recipe->save();

                // Save Ingredients
                if (!empty($recipeData['ingredients'])) {
                    $syncData = [];
                    foreach ($recipeData['ingredients'] as $ing) {
                        $dbIng = Ingredient::where('name', 'like', "%{$ing['name']}%")->first();
                        if (!$dbIng) {
                            $dbIng = Ingredient::create([
                                'name' => $ing['name'],
                                'image_path' => $ing['image'] ?? null
                            ]);
                        } else if (!empty($ing['image'])) {
                            $dbIng->image_path = $ing['image'];
                            $dbIng->save();
                        }
                        $syncData[$dbIng->id] = ['quantity' => $ing['amount']];
                    }
                    $recipe->ingredients()->sync($syncData);
                }

                return response()->json(['id' => $recipe->id]);
            }
            return response()->json(['error' => 'AI failed.'], 500);
        } catch (\Exception $e) {
            Log::error('Surprise Recipe Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
