<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'user_id', 'title', 'body', 'slug'
    ];
    
    public function user() {
        return $this->belongsTo(\App\User::class);
    }
    
    public function category() {
        return $this->belongsTo(\App\Category::class);
    }
    
    public function post_images() {
        return $this->hasMany(\App\PostImage::class);
    }
}
