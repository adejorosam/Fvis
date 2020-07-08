<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    protected $fillable = ['amount', 'last_growth', 'user_id'];
    
    protected $dates = ['created_at', 'updated_at', 'last_growth'];
    
    public function user() {
        return $this->belongsTo(\App\User::class);
    }
}
