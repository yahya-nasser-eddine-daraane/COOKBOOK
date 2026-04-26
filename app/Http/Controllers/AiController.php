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
                \"Short, distinct step 1 description.\",
                \"Short, distinct step 2 description.\",
                \"Short, distinct step 3 description.\"
            ]
        }
        IMPORTANT: Break down the instructions into MANY small, individual steps. Each step MUST be a separate string in the 'instructions' array. Avoid long paragraphs. Do not include step numbers.\";

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
            \"instructions\": [\"Short step 1\", \"Short step 2\"]
        }
        IMPORTANT: Break down the instructions into MANY small, individual steps. Each step MUST be a separate string in the 'instructions' array. Do not include step numbers.\";

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
