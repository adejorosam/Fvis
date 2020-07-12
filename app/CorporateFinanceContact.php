<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CorporateFinanceContact extends Model
{
    //
    protected $fillable = ['name','phoneNumber','email','address', 'typeOfBusiness', 'noOfEmployee', 'products'];
}
