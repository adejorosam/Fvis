<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RealEstateContact extends Model
{
    //
    protected $fillable = ['name','phoneNumber','email','address', 'typeOfBusiness', 'noOfEmployee', 'products','areaOfInterest'];
}
