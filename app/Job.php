<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    //
    protected $fillable = ['roleName','slug','jobID','jobDescription','responsibilties','skillsAndExperience',
                            'educationRequirement','jobLocation','closingDate', 'user_id'];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function jobApplications()
    {
        return $this->hasMany(\App\JobApplication::class);
    }
}
