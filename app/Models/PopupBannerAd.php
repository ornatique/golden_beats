<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PopupBannerAd extends Model
{
    use HasFactory;
    protected $appends = ['image_url'];
    protected $fillable = [
        'title',
        'image',
        'status',
    ];
     public function getImageUrlAttribute()
    {
        return $this->image
            ? asset($this->image)
            : null;
    }
}

