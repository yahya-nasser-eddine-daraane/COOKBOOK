<?php

use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::post('/api/login', [ApiAuthController::class, 'login']);
Route::post('/api/register', [ApiAuthController::class, 'register']);

Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.signin')->name('register');
Route::post('/logout', [ApiAuthController::class, 'logout'])->name('logout');

Route::get('/recipes/surprise', [RecipeController::class, 'surprise'])->name('recipes.surprise');
Route::get('/my-recipes', [RecipeController::class, 'myRecipes'])->name('recipes.my');
Route::resource('recipes', RecipeController::class);
Route::resource('categories', CategoryController::class);
Route::resource('ingredients', IngredientController::class);
