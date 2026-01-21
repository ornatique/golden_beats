<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
     protected $appends = ['image_url'];
    protected $fillable = [
        'category_id',
        'name',
        'image',
        'priority',
        'color'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
     public function getImageUrlAttribute()
    {
        return $this->image
            ? asset( $this->image)
            : null;
    }
}
