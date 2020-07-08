<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = ['name', 'amount'];
    
    public function users() {
        return $this->hasMany(\App\User::class);
    }
    
}
