<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\StoreRequestForms;
use App\Http\Requests\StoreContactForms;
use App\Http\Requests\StoreFinanceRequestForm;
use App\Http\Requests\StoreFixedInvestmentRequest;
use App\Http\Controllers\Controller;
use App\CommodityTradingContact;
use App\CorporateFinanceContact;
use App\FixedInvestmentContact;
use App\OilAndGasContact;
use App\ProjectFinanceContact;
use App\TalentDevContact;
use App\RealEstateContact;
use App\ProjectFinanceRequest;

class ServiceFormController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('forceJson');
    }


    
    public function storeOilAndGasForm(StoreRequestForms $request){
        
        $validated = $request->validated();
        $storedForm = OilAndGasContact::create($validated);
        if($storedForm){
            $response = [
                "success" => true,
                "message" => "Form was saved successfully",
                "data" => $storedForm
            ];
            return response()->json($response, 201);
        }
        else{
            $response = [
                "success" => false,
                "message" => "Error",
                "data" => null
            ];
            return response()->json($response, 401);
        }
    }


    public function storeTalentDevForm(StoreContactForms $request){
        
        $validated = $request->validated();
        $storedForm = TalentDevContact::create($validated);
        if($storedForm){
            $response = [
                "success" => true,
                "message" => "Form was saved successfully",
                "data" => $storedForm
            ];
            return response()->json($response, 201);
        }
        else{
            $response = [
                "success" => false,
                "message" => "Error",
                "data" => null
            ];
            return response()->json($response, 401);
        }

    }

    public function storeCommodityTradingForm(StoreContactForms $request){
        
        $validated = $request->validated();
        $storedForm = CommodityTradingContact::create($validated);
        if($storedForm){
            $response = [
                "success" => true,
                "message" => "Form was saved successfully",
                "data" => $storedForm
            ];
            return response()->json($response, 201);
        }
        else{
            $response = [
                "success" => false,
                "message" => "Error",
                "data" => null
            ];
            return response()->json($response, 401);
        }

    }

    public function storeCorporateFinanceForm(StoreContactForms $request){
        
        $validated = $request->validated();
        $storedForm = CorporateFinanceContact::create($validated);
        if($storedForm){
            $response = [
                "success" => true,
                "message" => "Form was saved successfully",
                "data" => $storedForm
            ];
            return response()->json($response, 201);
        }
        else{
            $response = [
                "success" => false,
                "message" => "Error",
                "data" => null
            ];
            return response()->json($response, 401);
        }
    }


    public function storeFixedInvestmentForm(StoreFixedInvestmentRequest $request){
        
        $validated = $request->validated();
        $storedForm = FixedInvestmentContact::create($validated);
        if($storedForm){
            $response = [
                "success" => true,
                "message" => "Form was saved successfully",
                "data" => $storedForm
            ];
            return response()->json($response, 201);
        }
        else{
            $response = [
                "success" => false,
                "message" => "Error",
                "data" => null
            ];
            return response()->json($response, 401);
        }
    }

    public function storeProjectFinanceForm(StoreContactForms $request){
        
        $validated = $request->validated();
        $storedForm = ProjectFinanceContact::create($validated);
        if($storedForm){
            $response = [
                "success" => true,
                "message" => "Form was saved successfully",
                "data" => $storedForm
            ];
            return response()->json($response, 201);
        }
        else{
            $response = [
                "success" => false,
                "message" => "Error",
                "data" => null
            ];
            return response()->json($response, 401);
        }
    }

    public function storeRealEstateForm(StoreRequestForms $request){
        
        $validated = $request->validated();
        $storedForm = RealEstateContact::create($validated);
        if($storedForm){
            $response = [
                "success" => true,
                "message" => "Form was saved successfully",
                "data" => $storedForm
            ];
            return response()->json($response, 201);
        }
        else{
            $response = [
                "success" => false,
                "message" => "Error",
                "data" => null
            ];
            return response()->json($response, 401);
        }

    }

    public function storeFinanceRequest(StoreFinanceRequestForm $request){
        
        $validated = $request->validated();
        $storedForm = ProjectFinanceRequest::create($validated);
        if($storedForm){
            $response = [
                "success" => true,
                "message" => "Form was saved successfully",
                "data" => $storedForm
            ];
            return response()->json($response, 201);
        }
        else{
            $response = [
                "success" => false,
                "message" => "Error",
                "data" => null
            ];
            return response()->json($response, 401);
        }
    }   
}
