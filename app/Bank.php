<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = ['user_id','bankname', 'bankcode', 'account_number', 'account_name'];
    
    public function user() {
        return $this->belongsTo(\App\User::class);
    }
    
    public function loans() {
        return $this->hasMany(\App\Loan::class);
    }
    
}
