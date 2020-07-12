<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CharityContact extends Model
{
    //
    public $fillable = ["name","phoneNumber","email","address","areaOfInterest"];
}
