<?php

use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AiController;

Route::post('/chef-ai/generate', [AiController::class, 'generateRecipe']);
Route::get('/chef-ai/surprise', [AiController::class, 'surpriseRecipe']);
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::post('/auth-v1/login', [ApiAuthController::class, 'login']);
Route::post('/auth-v1/register', [ApiAuthController::class, 'register']);

Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.signin')->name('register');
Route::post('/logout', [ApiAuthController::class, 'logout'])->name('logout');

Route::get('/recipes/surprise', [RecipeController::class, 'surprise'])->name('recipes.surprise');
Route::get('/my-recipes', [RecipeController::class, 'myRecipes'])->name('recipes.my');
Route::resource('recipes', RecipeController::class);
Route::resource('categories', CategoryController::class);
Route::resource('ingredients', IngredientController::class);
