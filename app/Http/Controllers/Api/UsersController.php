<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Bank;
use JWTAuth;
use App\Loan;
use App\User;
use App\Member;
use App\Mail\NewNotification;
use Illuminate\Support\Facades\Mail;
use Validator;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Transaction;
use Log;



class UsersController extends Controller
{
    
    public function stats(Request $request) {
        $user = JWTAuth::parseToken()->authenticate();
        $status = Loan::where('status', 'pending')->where('user_id', $user->id)->get();
        $active = Loan::where('status', 'active')->where('user_id', $user->id)->get();
        // $loans = $user->loans()->get();
        $loans = $user->loans()->jsonPaginate();
        
        return response()->json([
                'numPending' => count($status),
                'numActive' => count($active),
                'loans' => $loans
            ]);
    }
    
    public function getBanks(Request $request) {
        $user = JWTAuth::parseToken()->authenticate();
        $banks = Bank::where('user_id', $user->id)->get();
        
        return response()->json([
                'success' => true,
                'data' => $banks
            ]);
    }
    
    public function contactus(Request $request) {
        $objDemo = new \stdClass();
        $objDemo->message = "Email: {$request->email} <br><br> {$request->message}";
        $objDemo->sender = $request->first_name.' '.$request->last_name;
        $objDemo->receiver_name = 'FVIS Nigeria';
        $objDemo->date = \Carbon\Carbon::Now();
        $objDemo->subject = "New Contact Form!";
            
        Mail::to('info@fvisng.com')->send(new NewNotification($objDemo));
        
        return response()->json([
                'success' => true,
                'data' => 'Message sent successfully'
            ]);
    }
    
    public function updateprofile(Request $request) {
        $user = JWTAuth::parseToken()->authenticate();
        
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        $user->nationality = $request->nationality;
        $user->state_of_origin = $request->state;
        $user->lga_of_origin = $request->lgaoforigin;
        $user->lga_of_residence = $request->lgaofresidence;
        $user->state_of_residence = $request->stateofresidence;
        $user->residential_address = $request->residential_address;
        $user->marital_status = $request->marital_status;
        $user->dob = $request->dob;
        if($user->save()) {
            return response()->json([
                    'success' => true,
                    'data' => 'Profile Successfully Updated'
                ]);
        }
        
        return response()->json([
                'success' => false,
                'error' => 'An error occured while updating your profile, kindly try again.'
            ]);
        
    }
    
    public function updatepic(Request $request) {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|max:2048',
        ]);
        if($validator->fails()) {
            return response()->json([
                'success'=> false,
                'error'=> $validator->messages()
            ]);
        }
      if ($request->hasFile('image')) {
            $imagePath = $request->file('image');
            // $imageName = time() . '.' . $imagePath ->getClientOriginalExtension();
            $imageName = time() . '_' . $imagePath->getClientOriginalName();
            // $imagePath ->move('fv-contents', $imageName );
        
          if($request->image->storeAs('public/profile_img', $imageName)) {
              $user = JWTAuth::parseToken()->authenticate();
              $user->profile_img = "https://fvisng.com/assets/images/profile_img/{$imageName}";
              $user->save();
              return response()->json([
                  'success' => true,
                  'data' => $imageName
                  ]);
          }
          
          
      } else {
          return response()->json([
                'success' => false,
                'error' => 'File not uploaded/not found'
              ]);
      }
    }
    
    public function fundstransfer(Request $request) {
        $create = Http::withToken('sk_test_790eaede6d03efcc711663435d90a7e2364e42b3')->post('https://api.paystack.co/transferrecipient', [
                'type' => 'nuban',
                'name' => $request->account_name,
                'account_number' => $request->account_number,
                'bank_code' => $request->bankcode,
                'currency' => 'NGN'
            ]);
            $body = $create->json();
            
            $tf_id = $body['data']['id'];
        $response = Http::withToken('sk_test_790eaede6d03efcc711663435d90a7e2364e42b3')->post('https://api.paystack.co/transfer', [
            'source' => 'balance',
            'currency' => 'NGN',
            'reason' => $request->remark,
            'recipient' => $tf_id
        ]);
        
        $tf = $response->json();
        if($tf['status'] == true) {
            return response()->json([
                    'success' => true,
                    'data' => 'Transfer Successful',
                    'info' => $tf
                ]);
        } else {
            return response()->json([
                    'success' => false,
                    'error' => 'An error occured while processing your transfer'
                ]);
        }
        
    }
    
    public function cron() {
        $loans = Loan::where('repayment_date', null)->get();
          foreach($loans as $loan){
              //get user
            //   $user = User::where('id',$loan->user_id)->first();
              
              if($loan->status == 'active'){
                  $dtime = $loan->approved_date->addDays($loan->duration);
                  $dtme = \Carbon\Carbon::now()->subDays(1)->toDateTimeString();
                  
                  if(Carbon::now()->greaterThan($dtime) && $loan->last_interest !== null) {
                      if($loan->last_interest >= $dtme) {
                        $loan->status = 'overdue';
                        $loan->last_interest = Carbon::now();
                        $loan->final_amount = $loan->amount + $loan->interest;
                        $loan->save();    
                      }
                      
                  } else if(Carbon::now()->greaterThan($dtime) && $loan->last_interest == null) {
                      $loan->status = 'overdue';
                      $loan->last_interest = Carbon::now();
                      $loan->final_amount = $loan->final_amount + $loan->interest;
                      $loan->save();
                  }
              } else if($loan->status == 'overdue') {
                  $dtme = Carbon::now()->subDays(1)->toDateTimeString();
                  $check = Carbon::parse($dtme)->greaterThanOrEqualTo($loan->last_interest);
                //   Log::info($check);
                  if($check) {
                        $loan->final_amount = $loan->final_amount + $loan->interest;
                        $loan->last_interest = Carbon::now();
                        $loan->save();    
                      }
              }
          }
    }
    
    public function repayloan(Request $request) {
        $user = JWTAuth::parseToken()->authenticate();
        $loan = Loan::where('user_id', $user->id)->orderBy('id', 'desc')->first();
        $ref = "https://api.paystack.co/transaction/verify/$request->ref";
        $response = Http::withToken('sk_test_790eaede6d03efcc711663435d90a7e2364e42b3')->get($ref);
        $body = $response->json();
        
        if($body['status'] == 'success') {
            if($loan->status == 'active') {
                $maths = ((int)$loan->final_amount * 5) / 100;
                $amount = (int)$loan->final_amount - $maths;
                
                $loan->status = 'paid';
                $loan->repayment_date = Carbon::now();
                $loan->save();
            
                
                $transaction = new Transaction();
                $transaction->type = 'loan';
                $transaction->amount = $loan->final_amount;
                $transaction->status = $request->status;
                $transaction->message = $request->message;
                $transaction->reference = $request->ref;
                $transaction->user()->associate($user);
                $transaction->loan()->associate($loan);
                $transaction->description = "$user->first_name loan repayment";
                $transaction->save();
                return response()->json([
                    'success' => true,
                    'data' => 'Loan Repayment Successful'
                ]);    
                } else {
                    return response()->json([
                        'success' => false,
                    'error' => 'An error occured while processing your transaction',
                    'data' => $body['status'],
                    'loan' => $loan
                            ]);
                }
                
            } else {
            return response()->json([
                    'success' => false,
                    'error' => 'An error occured while processing your transaction'
                ]);
        }
    }
        
        public function topup(Request $request) {
            $ref = "https://api.paystack.co/transaction/verify/$request->ref";
            $response = Http::withToken('sk_test_790eaede6d03efcc711663435d90a7e2364e42b3')->get($ref);
            $body = $response->json();
            if($body['status'] == 'success') {
                $user = auth()->user();
                
                $user->wallet += $request->amount;
                $user->save();
                
                $transaction = new Transaction();
                $transaction->type = 'Wallet Topup';
                $transaction->amount = $request->amount;
                $transaction->reference = $request->ref;
                $transaction->status = 'successful';
                $transaction->description = $user->first_name .' wallet deposit';
                $transaction->user_id = $user->id;
                $transaction->save();
                
                return response()->json([
                        'success' => true,
                        'data' => $user
                    ]);
            } else {
                return reponse()->json([
                        'success' => false,
                        'msg' => 'Transaction not successful'
                    ]);
            }
        }

        public function subscription(Request $request) {
           
            $ref = "https://api.paystack.co/transaction/verify/$request->id";
            $response = Http::withToken('sk_test_d44373e3a61b1d4c5e03fac8b65e2f3ac1393a97')->get($ref);
            $body = $response->json();
            if($body['status'] === 'success') {
                if($request->amount === 125000){
                    $user = JWTAuth::parseToken()->authenticate();
                    // Bronze Membership
                    $user->member_id = 1; 
                    if($user->update()){
                        $response = [
                            "success" => true,
                            "message" => "Successfully subscribed",
                            "data" => $user
                        ];
                        return response()->json($response, 201);
                    }
                    else{
                        $response = [
                            "success" => false,
                            "message" => "An error occured",
                            "data" => null
                        ];
                        return response()->json($response, 401);
                    }
                }
                elseif($request->amount === 250000){
                    $user = JWTAuth::parseToken()->authenticate();
                    // Silver Membership
                    $user->member_id = 2;
                    if($user->update()){
                        $response = [
                            "success" => true,
                            "message" => "Successfully subscribed",
                            "data" => $user
                        ];
                        return response()->json($response, 201);
                    }
                    else{
                        $response = [
                            "success" => false,
                            "message" => "An error occured",
                            "data" => null
                        ];
                        return response()->json($response, 401);
                    }
                }
                elseif($request->amount === 600000){
                    $user = JWTAuth::parseToken()->authenticate();
                    // Gold Membership
                    $user->member_id = 3;
                    if($user->update()){
                        $response = [
                            "success" => true,
                            "message" => "Successfully subscribed",
                            "data" => $user
                        ];
                        return response()->json($response, 201);
                    }
                    else{
                        $response = [
                            "success" => false,
                            "message" => "An error occured",
                            "data" => null
                        ];
                        return response()->json($response, 401);
                    }
                }
                elseif($request->amount === 5000000){
                    $user = JWTAuth::parseToken()->authenticate();
                    // Diamond Membership
                    $user->member_id = 4;
                    if($user->update()){
                        $response = [
                            "success" => true,
                            "message" => "Successfully subscribed",
                            "data" => $user
                        ];
                        return response()->json($response, 201);
                    }
                    else{
                        $response = [
                            "success" => false,
                            "message" => "An error occured",
                            "data" => null
                        ];
                        return response()->json($response, 401);
                    }
                }
                elseif($request->amount === 20000000){
                    $user = JWTAuth::parseToken()->authenticate();
                    if($member->save()){
                        // Platinum Membership
                        $user->member_id = 5;                       
                        $user->update();
                        $response = [
                            "success" => true,
                            "message" => "Successfully subscribed",
                            "data" => $user
                        ];
                        return response()->json($response, 201);
                    }
                    else{
                        $response = [
                            "success" => false,
                            "message" => "An error occured",
                            "data" => null
                        ];
                        return response()->json($response, 401);
                    }
                }   
            }
            else {
                return response()->json([
                        'success' => false,
                        'msg' => 'Transaction not successful'
            ], 500);
             }
        }
    
    
    public function webhook(Request $request) {
        Log::info($request->all());
        Log::info(getallheaders());
    }

}
