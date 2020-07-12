<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequestForms extends FormRequest
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
            //
            'name' => "required|string",
            'phoneNumber' => 'required',
            'email' => 'required|email',
            'address' => "required|string",
            'typeOfBusiness' => "required|string",
            'noOfEmployee' => "required|string",
            'products' => "required|string",
            'areaOfInterest' => "required|string"
        ];
    }
}
