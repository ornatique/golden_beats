<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReelLike extends Model
{
    protected $fillable = ['reel_id','user_id'];

    public function reel()
    {
        return $this->belongsTo(Reel::class);
    }
}
