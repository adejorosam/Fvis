<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['type', 'amount', 'reference', 'status', 'description', 'user_id', 'loan_id'];
    
    public function user() {
        return $this->belongsTo(\App\User::class);
    }
    
    public function loan() {
        return $this->belongsTo(\App\Loan::class);
    }
}
