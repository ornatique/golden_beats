<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reel extends Model
{
    protected $fillable = [
        'name','description','image','media_file',
        'category_id','subcategory_id'
    ];

    public function likes()
    {
        return $this->hasMany(ReelLike::class);
    }

    public function comments()
    {
        return $this->hasMany(ReelComment::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }
}

