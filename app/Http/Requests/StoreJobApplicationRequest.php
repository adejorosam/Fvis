<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [ 
                'country'=> "required|string",
                'legalGivenName'=> "required|string",
                'legalFamilyName'=> "required|string",
                'isHavePreferredName'=> "required|string",
                'preferredGivenName'=> "required|string",
                'preferredFamilyName'=> "required|string",
                'address'=> "required|string",
                'email'=> "required|email",
                'phoneNumber'=> "required",
                'sourceOfInfo'=> "required|string",
                'isWorkedBefore'=> "required",
                'relevantWebsites'=> "required|string",
                'linkedinProfileUrl'=> "required|url",
                'resume'=> 'required|file|mimes:pdf,txt|max:2048',
                'user_id'=> "required",
                'job_id'=> "required",
            ];
    }
}
