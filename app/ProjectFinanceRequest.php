<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectFinanceRequest extends Model
{
    //
    protected $fillable = ["firstName" ,
                            "lastName" ,
                            "email",
                            "companyAddress",
                            "companyAddress2",
                            "postalCode",
                            "state",
                            "city",
                            "businessType",
                            "companyWebsite",
                            "companyName",
                            "phoneNumber" ,
                            "projectCountry" ,
                            "projectDescription" ,
                            "projectedCost" ,
                            "totalAmountSpent" ,
                            "totalAmountRequested" ,
                            "sourceOfRequiredEntity" ,
                            "isLandOwner" ,
                            "isNeedDevelopmentPartner" ,
                            "isApprovalsComplete" ,
                            "isHaveDevelopmentPartner" ,
                            "isEngineeringComplete" ,
                            "isConstructionBegun" ,
                            "signature" ];

    
}

            