<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Recipe;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        
        
        $featuredTitles = ['Tanjia', 'Royal Couscous', 'Tiramisu', 'Strawberry Cheesecake', 'Green Smoothie Bowl','Paella' ];
        
        $recipes = Recipe::with('user', 'category')
            ->withCount('viewers')
            ->whereIn('title', $featuredTitles)
            ->get();
            
        // If we don't have 6 featured recipes, pad with the newest ones
        if ($recipes->count() < 6) {
            $excludeIds = $recipes->pluck('id')->toArray();
            $moreRecipes = Recipe::with('user', 'category')
                ->withCount('viewers')
                ->whereNotIn('id', $excludeIds)
                ->latest()
                ->take(6 - $recipes->count())
                ->get();
            $recipes = $recipes->merge($moreRecipes);
        }
    
        return view('home', compact('categories', 'recipes'));
    }
}
