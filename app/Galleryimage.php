<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Galleryimage extends Model
{
    protected $fillable = ['gallery_url', 'gallery_id'];
    public function gallery() {
        return $this->belongsTo(\App\Gallery::class);
    }
}
