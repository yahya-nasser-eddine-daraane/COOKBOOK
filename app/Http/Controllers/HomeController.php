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
        $recipes = Recipe::with('user', 'category')->withCount('viewers')->latest()->take(6)->get();
    
        return view('home', compact('categories', 'recipes'));
    }
}
