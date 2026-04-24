<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = ['title', 'description', 'instructions', 'prep_time', 'user_id', 'category_id', 'image_path', 'servings', 'calories'];

   public function ingredients()
   {
        return $this->belongsToMany(Ingredient::class)->withPivot('quantity');
   }

   public function category()
   {
       return $this->belongsTo(Category::class);
   }
   public function user()
   {
       return $this->belongsTo(User::class);
   } 

   public function viewers()
   {
       return $this->belongsToMany(User::class, 'recipe_viewers')->withTimestamps();
   }
}
