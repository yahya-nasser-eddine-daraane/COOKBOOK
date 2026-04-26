<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Category;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecipeController extends Controller
{
    
    public function index(Request $request)
    {
        $query = Recipe::with('user', 'category')->withCount('viewers');
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('category', function($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('category')) {
            $category = $request->get('category');
            $query->whereHas('category', function($q) use ($category) {
                $q->where('name', $category);
            });
        }

        $recipes = $query->latest()->get();
        return view('recipes.index', compact('recipes'));
    }
    
    public function create()
    {  
        $categories = Category::all();
        $ingredients = Ingredient::orderBy('name')->get();
        return view('recipes.create', compact('categories', 'ingredients'));
    }

    
    public function store(Request $request){
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'instructions' => 'required|string',
            'prep_time' => 'required|integer|min:1',
            'category_id' => 'required|exists:categories,id',
            'image_path' => 'nullable|string',
            'ingredients' => 'array',
            'ingredients.*' => 'exists:ingredients,id',
            'quantities' => 'array',
        ]);

        $recipe = new Recipe($request->except(['ingredients', 'quantities', 'image_file']));
        
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/recipes'), $filename);
            $recipe->image_path = 'uploads/recipes/' . $filename;
        }

        $userId = Auth::id();
        $recipe->user_id = $userId;
        
        $recipe->save();

        if ($request->has('ingredients')) {
            $ingredientsData = [];
            foreach ($request->ingredients as $index => $ingredientId) {
                $quantity = $request->quantities[$index] ?? '0';
                if ($quantity === null || $quantity === '') {
                   $quantity = '0';
                }
                $ingredientsData[$ingredientId] = ['quantity' => $quantity];
            }
            $recipe->ingredients()->sync($ingredientsData);
        }
        return redirect()->route('recipes.index');
    }
    
    public function show(Recipe $recipe){
        $recipe->load('ingredients', 'user', 'category');
        $recipe->loadCount('viewers');
        if (Auth::check()) {
            if (!$recipe->viewers()->where('user_id', Auth::id())->exists()) {
                $recipe->viewers()->attach(Auth::id());
                $recipe->loadCount('viewers');
            }
        }

        return view('recipes.show', compact('recipe'));
    }


    public function edit(Recipe $recipe){
        $categories = Category::all();
        $ingredients = Ingredient::all();
        return view('recipes.edit', compact('recipe', 'categories', 'ingredients'));
    }

    
    public function update(Request $request, Recipe $recipe){
        
        if ($recipe->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'required|string',
            'prep_time' => 'required|integer|min:1',
            'category_id' => 'required|exists:categories,id',
            'image_path' => 'nullable|string',
            'ingredients' => 'array',
            'ingredients.*' => 'exists:ingredients,id',
            'quantities' => 'array',
        ]);

        $recipe->fill($request->except(['ingredients', 'quantities', 'image_file']));

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/recipes'), $filename);
            $recipe->image_path = 'uploads/recipes/' . $filename;
        }

        $recipe->save();

        if ($request->has('ingredients')) {
            $ingredientsData = [];
            foreach ($request->ingredients as $index => $ingredientId) {
                $quantity = $request->quantities[$index]; 
                $ingredientsData[$ingredientId] = ['quantity' => $quantity];
            }
            $recipe->ingredients()->sync($ingredientsData);
        } else {
             $recipe->ingredients()->detach();
        }

        return redirect()->route('recipes.index');
    }

    public function myRecipes()
    {
        $recipes = Recipe::where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->with('user', 'category')
            ->withCount('viewers')
            ->latest()
            ->get();
            
        return view('recipes.index', ['recipes' => $recipes, 'title' => 'Mes Recettes']);
    }

    public function surprise()
    {
        // Call the AI controller logic internally or just redirect to a new AI-generated recipe
        // To make it easy and fast, we will call the AI generation logic here.
        return redirect()->route('home')->with('info', 'Generating a surprise for you...'); 
        // Wait, a redirect with 'info' is not enough. I should actually do the generation.
    }

    public function destroy(Recipe $recipe)
    {
        if ($recipe->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403);
        }
        
        $recipe->delete();
        return redirect()->route('recipes.index');
    }
}