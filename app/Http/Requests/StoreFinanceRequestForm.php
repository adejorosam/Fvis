<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFinanceRequestForm extends FormRequest
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
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email',
            'phoneNumber' => 'required',
            "companyAddress" => 'string',
            "companyAddress2" => "string",
            "postalCode" => 'integer',
            "state" => "string",
            "city" => "string",
            "businessType" => "string",
            "companyWebsite" => "url",
            "companyName" => 'string',
            'projectCountry' => 'required',
            'projectDescription' => 'required',
            'projectedCost' => 'required',
            'totalAmountSpent' => 'required',
            'totalAmountRequested' => 'required',
            'sourceOfRequiredEntity' => 'required',
            'isLandOwner' => 'required',
            'isNeedDevelopmentPartner' => 'required',
            'isApprovalsComplete' => 'required',
            'isHaveDevelopmentPartner' => 'required',
            'isEngineeringComplete' => 'required',
            'isConstructionBegun' => 'required',
            'signature' => 'required'

        ];
    }
}