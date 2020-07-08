<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name', 'category', 'type', 'description', 'budget', 'proposal', 'user_id'];
    
    public function user() {
        return $this->belongsTo(\App\User::class);
    }
}
