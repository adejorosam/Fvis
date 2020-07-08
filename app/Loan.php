<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'amount', 'interest', 'duration', 'final_amount', 'user_id', 'credit_score', 'ref', 'purpose', 'status'
    ];
    
    protected $dates = ['created_at', 'updated_at', 'approved_date', 'repayment_date', 'last_interest'];
    
    public function user() {
        return $this->belongsTo(\App\User::class);
    }
    
    public function bank() {
        return $this->belongsTo(\App\Bank::class);
    }
    
    public function transactions() {
        return $this->hasMany(\App\Transaction::class);
    }
}
