<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'password'];

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    public function viewedRecipes()
    {
        return $this->belongsToMany(Recipe::class, 'recipe_viewers')->withTimestamps();
    }
}
?>