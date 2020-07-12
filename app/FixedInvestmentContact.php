<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FixedInvestmentContact extends Model
{
    //
    protected $fillable = ["firstName","lastName","investmentVolume","phoneNumber","email", "title"];
}

