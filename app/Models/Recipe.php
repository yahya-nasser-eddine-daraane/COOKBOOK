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
           'italian' => [
               'https://images.unsplash.com/photo-1498579150354-977475b7ea0b?w=600&q=80',
               'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=600&q=80',
               'https://images.unsplash.com/photo-1595295333158-4742f28fbd85?w=600&q=80',
               'https://images.unsplash.com/photo-1473093295043-cdd812d0e601?w=600&q=80',
               'https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?w=600&q=80'
           ],
           'asian' => [
               'https://images.unsplash.com/photo-1563379926898-05f4575a45d8?w=600&q=80',
               'https://images.unsplash.com/photo-1553621042-f6e147245754?w=600&q=80',
               'https://images.unsplash.com/photo-1585032226651-759b368d7246?w=600&q=80',
               'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=600&q=80',
               'https://images.unsplash.com/photo-1552611052-33e04de081de?w=600&q=80'
           ],
           'mexican' => [
               'https://images.unsplash.com/photo-1565299585323-38d6b0865b47?w=600&q=80',
               'https://images.unsplash.com/photo-1615870216519-2f9fa575fa5c?w=600&q=80',
               'https://images.unsplash.com/photo-1551504734-5ee1c4a1479b?w=600&q=80'
           ],
           'middle eastern' => [
               'https://images.unsplash.com/photo-1541518763669-27fef04b14ea?w=600&q=80',
               'https://images.unsplash.com/photo-1529692236671-f1f6cf9683ba?w=600&q=80'
           ],
           'moroccan' => [
               'https://images.unsplash.com/photo-1541518763669-27fef04b14ea?w=600&q=80',
               'https://images.unsplash.com/photo-1529692236671-f1f6cf9683ba?w=600&q=80'
           ],
           'african' => [
               'https://images.unsplash.com/photo-1541518763669-27fef04b14ea?w=600&q=80',
               'https://images.unsplash.com/photo-1529692236671-f1f6cf9683ba?w=600&q=80'
           ],
           'american' => [
               'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600&q=80',
               'https://images.unsplash.com/photo-1550547660-d9450f859349?w=600&q=80',
               'https://images.unsplash.com/photo-1460306855393-0410f61241c7?w=600&q=80'
           ],
           'breakfast' => [
               'https://images.unsplash.com/photo-1533089860892-a7c6f0a88666?w=600&q=80',
               'https://images.unsplash.com/photo-1494859802809-d069c3b71a8a?w=600&q=80',
               'https://images.unsplash.com/photo-1482049016688-2d3e1b311543?w=600&q=80'
           ],
           'dessert' => [
               'https://images.unsplash.com/photo-1551024506-0bccd828d307?w=600&q=80',
               'https://images.unsplash.com/photo-1495147466023-ac5c588e2e94?w=600&q=80',
               'https://images.unsplash.com/photo-1514517521153-1be72277b32f?w=600&q=80'
           ],
           'healthy' => [
               'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=600&q=80',
               'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=600&q=80',
               'https://images.unsplash.com/photo-1490645935967-10de6ba17061?w=600&q=80'
           ]
       ];

       foreach ($map as $key => $urls) {
           if (str_contains($categoryName, $key)) {
               $index = $this->id ? ($this->id % count($urls)) : 0;
               return $urls[$index];
           }
       }

       $generics = [
           'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=600&q=80',
           'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&q=80',
           'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=600&q=80',
           'https://images.unsplash.com/photo-1473093295043-cdd812d0e601?w=600&q=80',
           'https://images.unsplash.com/photo-1490645935967-10de6ba17061?w=600&q=80',
           'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=600&q=80',
           'https://images.unsplash.com/photo-1565958011703-44f9829ba187?w=600&q=80',
           'https://images.unsplash.com/photo-1482049016688-2d3e1b311543?w=600&q=80',
           'https://images.unsplash.com/photo-1495521821757-a1efb6729352?w=600&q=80'
       ];

       $index = $this->id ? ($this->id % count($generics)) : 0;
       return $generics[$index];
   }
}
