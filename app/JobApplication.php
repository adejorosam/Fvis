<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    //
    protected $fillable = [ 'country','legalGivenName','legalFamilyName','isHavePreferredName','preferredGivenName',
                            'preferredFamilyName','address','email','phoneNumber','sourceOfInfo',
                            'isWorkedBefore','relevantWebsites','linkedinProfileUrl','resume','user_id',
                            'job_id'];


    public function job()
    {
        return $this->belongsTo(\App\Job::class);
    }
}
