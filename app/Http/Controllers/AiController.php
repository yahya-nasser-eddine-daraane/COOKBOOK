<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        IMPORTANT: Break down the instructions into MANY small steps. Each string in the 'instructions' array MUST contain ONLY ONE single action. Never combine multiple tasks (like 'cook and cool') into one step.\";

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
        $prompt = "Generate a random, creative recipe. Return ONLY a JSON object with this structure:
        {
            \"id\": \"surprise_rand\",
            \"title\": \"Title\",
            \"category\": \"Dinner\",
            \"image\": \"A relevant high-quality food image URL\",
            \"meta\": {\"time\": 30, \"servings\": 2, \"calories\": 400},
            \"ingredients\": [{\"name\": \"Ing\", \"amount\": \"Amt\"}],
            \"instructions\": [\"Action 1\", \"Action 2\"]
        }
        IMPORTANT: Each step MUST contain ONLY ONE single action. Break down the recipe into as many steps as possible.\";

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
                    'temperature' => 0.9
                ]);

            if ($response->successful()) {
                $recipeData = json_decode($response->json('choices.0.message.content'), true);
                $recipeData['id'] = 'surprise_' . uniqid();
                return response()->json($recipeData);
            }
            return response()->json(['error' => 'AI failed.'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
