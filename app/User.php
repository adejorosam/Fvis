<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'last_name', 'first_name', 'email', 'password', 'phone_number', 'bvn', 'scope', 'lga_of_origin', 'lga_of_residence', 'marital_status', 'nationality', 'residential_address', 'state_of_origin', 'state_of_residence', 'dob', 'mobile_number', 'wallet'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'bvn'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    public function loans() {
        return $this->hasMany(\App\Loan::class);
    }
    
    public function banks() {
        return $this->hasMany(\App\Bank::class);
    }
    
    public function transactions() {
        return $this->hasMany(\App\Transaction::class);
    }
    
    public function projects() {
        return $this->hasMany(\App\Project::class);
    }
    
    public function investments() {
        return $this->hasMany(\App\Investment::class);
    }
    
    public function member() {
        return $this->belongsTo(\App\Member::class);
    }
    
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
