<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = ['title', 'user_id', 'slug'];
    
    public function user() {
        return $this->belongsTo(\App\User::class);
    }
    
    public function galleryimages() {
        return $this->hasMany(\App\Galleryimage::class);
    }
}
