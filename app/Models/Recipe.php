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

   public function getFallbackImageAttribute()
   {
       $categoryName = $this->category ? strtolower($this->category->name) : '';
       
       $map = [
           'italian' => 'https://images.unsplash.com/photo-1498579150354-977475b7ea0b?w=600&q=80',
           'asian' => 'https://images.unsplash.com/photo-1563379926898-05f4575a45d8?w=600&q=80',
           'mexican' => 'https://images.unsplash.com/photo-1565299585323-38d6b0865b47?w=600&q=80',
           'middle eastern' => 'https://images.unsplash.com/photo-1596450514735-a50d2105151b?w=600&q=80',
           'american' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600&q=80',
           'breakfast' => 'https://images.unsplash.com/photo-1533089860892-a7c6f0a88666?w=600&q=80',
           'dessert' => 'https://images.unsplash.com/photo-1551024506-0bccd828d307?w=600&q=80',
           'healthy' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=600&q=80',
           'african' => 'https://images.unsplash.com/photo-1541518763669-27fef04b14ea?w=600&q=80',
           'moroccan' => 'https://images.unsplash.com/photo-1541518763669-27fef04b14ea?w=600&q=80',
           'european' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=600&q=80',
           'south american' => 'https://images.unsplash.com/photo-1615486171448-43dbaf612953?w=600&q=80',
       ];

       foreach ($map as $key => $url) {
           if (str_contains($categoryName, $key)) {
               return $url;
           }
       }

       return 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?w=600&q=80'; // Generic delicious food
   }
}
