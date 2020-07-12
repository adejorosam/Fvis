<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OilAndGasContact extends Model
{
    //
    protected $fillable = ['name','phoneNumber','email','address', 'typeOfBusiness', 'noOfEmployee', 'products','areaOfInterest'];
}
