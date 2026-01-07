<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'image',
        'priority',
        'home',
        'color',
        'shape'
    ];

     public function subcategories()
    {
        return $this->hasMany(Subcategory::class, 'category_id');
    }
}

