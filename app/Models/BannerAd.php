<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerAd extends Model
{
    use HasFactory;
    protected $appends = ['image_url'];
    protected $fillable = [
        'product_id',
        'category_id',
        'subcategory_id',
        'image',
    ];

    public function category() {
    return $this->belongsTo(Category::class);
}

public function subcategory() {
    return $this->belongsTo(Subcategory::class);
}

public function product() {
    return $this->belongsTo(Product::class);
}


    public function getImageUrlAttribute()
    {
        return $this->image
            ? asset( $this->image)
            : null;
    }

}
