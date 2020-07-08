<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use JWTAuth;
use App\User;
use App\Bank;
use App\Loan;
use Validator;

class LoansController extends Controller
{
    protected $user;

    public function __construct() {
        $this->user = JWTAuth::parseToken()->authenticate();
    }
    
    public function bvnlookup(Request $request) {
        // $rand = "fvisng-";
    $rand = substr(uniqid('', true), -8);
    $rand .= substr(uniqid('', true), -5);
    $rand .= substr(uniqid('', true), -5);

    $secretKey = "uF4z5FVWBvvBBONE";
    $signatureHash = md5($rand.';'.$secretKey);
    
    $headers = array(
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer: 1a1aXwsCryTMZhPp0ckV_6f3230791a7542c7b6031d21aa033c37',
        'Signature' => $signatureHash
    );
    $query = array(
        'request_ref' => $rand,
        'request_type' => 'bvn_lookup',
        'auth' => array(
            'type' => null,
            'secure' => null,
            'auth_provider' => 'SunTrust'
        ),
        'transaction' => array(
            'amount' => null,
            'transaction_ref' => $rand,
            'transaction_desc' => $request->desc,
            'transaction_ref_parent' => null,
            'customer' => array(
                'customer_ref' => '',
                'firstname' => '',
                'surname' => '',
                'email' => '',
                'mobile_no' => ''
            ),
            'details' => array(
                'bvn' => $request->bvn,
                'otp_validation' => false
            )
        )
    );

    $body = \Unirest\Request\Body::json($query);

    $response = \Unirest\Request::post('https://api.onepipe.io/v1/generic/transact', $headers, $body);
        if ($response->body->data->provider_response_code == 00) {
        $res = $response->body->data->provider_response;
        
        
        $user = auth()->user();
        $user->lga_of_origin = $res->lgaOfOrigin;
        $user->lga_of_residence = $res->lgaOfResidence;
        $user->marital_status = $res->maritalStatus;
        $user->nationality = $res->nationality;
        $user->residential_address = $res->residentialAddress;
        $user->state_of_origin = $res->stateOfOrigin;
        $user->state_of_residence = $res->stateOfResidence;
        $user->dob = $res->dateOfBirth;
        $user->bvn = $this->encrypt_decrypt('encrypt', $res->bvn);
        $user->mobile_number = $res->phoneNumber1;
        // $user->save();

            return response()->json([
                'success' => true,
                // 'message' => $response,
                // 'request' => $request->all()
            ]);
        }
        return response()->json([
                'success' => false,
                'data' => 'Invalid BVN entered!',
                'message' => $response,
                'request' => $request->all()
            ]);
    }
    
    public function creditscore(Request $request) {
        $user = JWTAuth::authenticate($request->token);
        $rand = substr(uniqid('', true), -8);
        $rand .= substr(uniqid('', true), -5);
        $rand .= substr(uniqid('', true), -5);

        $secretKey = "uF4z5FVWBvvBBONE";
        $signatureHash = md5($rand.';'.$secretKey);
        $headers = array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer 1a1aXwsCryTMZhPp0ckV_6f3230791a7542c7b6031d21aa033c37',
            'Signature' => $signatureHash
        );
        $mobile = "234".substr($user->mobile_number, -10, 10);
        $query = array(
            'request_ref' => $rand,
            'transaction' => array(
                'amount' => $request->amount,
                'transaction_ref' => $rand,
                'transaction_desc' => $request->desc,
                'currency' => 'NGN',
                'algo_code' => 'markovstats1.0',
                'customer' => array(
                    'customer_ref' => $mobile,
                    'firstname' => $request->first_name,
                    'surname' => $request->last_name,
                    'email' => $request->email,
                    'mobile_no' => $mobile
                )
            )
        );
        
        $body = \Unirest\Request\Body::json($query);

        $response = \Unirest\Request::post('https://api.onepipe.io/v1/loans/score', $headers, $body);

         if ($response->body->status == 'Successful') {
             $res = $response->body->data->score;
             if($res->confidence < 10) {
                 $conf = $res->confidence * 10;
             } else {
                 $conf = $res->confidence;
             }
             $checkbank = Bank::where('bankcode', $request->bankcode)->where('account_number', $request->account_number)->first();
        if($checkbank) {
            $bankid = $checkbank->id;
        } else {
            $bank = new Bank();
            $bank->user()->associate($user);
            $bank->bankcode = $request->bankcode;
            $bank->account_number = $request->account_number;
            $bank->account_name = $request->account_name;
            $bank->bankname = $request->selectedbank;
            $bank->save();
            
            $bankid = $bank->id;
        }
            
            $loan = new Loan();
            $loan->amount = $request->amount;
            $loan->interest = $request->interest;
            $loan->final_amount = (int)$request->amount + (int)$request->interest;
            $loan->credit_score = $conf;
            $loan->ref = $rand;
            $loan->purpose = $request->purpose;
            $loan->bank_id = $bankid;
            $loan->status = 'pending';
            $loan->duration = $request->duration;
            if ($this->user->loans()->save($loan))
            return response()->json([
                'success' => true,
                'ref' => $loan->ref
            ]);
         }
         
         return response()->json([
                'success' => false,
                'data' => $response,
                'query' => $query
             ]);
    }
    
    public function encrypt_decrypt($action, $string)
        {
            $output = false;
 
            $encrypt_method = "AES-256-CBC";
            $secret_key = 'This is my secret key';
            $secret_iv = 'This is my secret iv';
 
            // hash
            $key = hash('sha256', $secret_key);
 
            // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a
            // warning
            $iv = substr(hash('sha256', $secret_iv), 0, 16);
 
            if ($action == 'encrypt')
            {
                $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
                $output = base64_encode($output);
            }
            else
            {
                if ($action == 'decrypt')
                {
                    $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
                }
            }
 
            return $output;
        }
        
    public function userloans($id) {
        $user = auth()->user();
        $find = Loan::where('user_id', $user->id)->orderBy('id', 'desc')->jsonPaginate($id);
        if($find) {
            return response()->json([
                'success' => true,
                'data' => $find
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'No record found'
        ], 404);
    }
    
    public function submitloan(Request $request) {
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'purpose' => 'required',
            'duration' => 'required',
            'interest' => 'required',
            'relativename' => 'required',
            'relativename' => 'required',
            'employ_status' => 'required',
            'bankcode' => 'required',
            'account_number' => 'required',
            'account_name' => 'required',
            'idback' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'idfront' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        if($validator->fails()) {
            return response()->json([
                'success'=> false,
                'error'=> $validator->messages()
            ]);
        }
        $user = auth()->user();
        $rechk = Loan::where('user_id', $user->id)->where('status', 'active')->first();
        if($rechk) {
            return response()->json([
                    'success' => false,
                    'error' => 'You already have an active loan.'
                ]);
        }
        $rechkk = Loan::where('user_id', $user->id)->where('status', 'overdue')->first();
        if($rechkk) {
            return response()->json([
                    'success' => false,
                    'error' => 'You already have an active loan.'
                ]);
        }
        
        if(is_null($user->relativename)) {
            $user->relativename = $request->relativename;
        }
        if(is_null($user->relativenumber)) {
            $user->relativenumber = $request->relativenumber;
        }
        if(is_null($user->employ_status)) {
            $user->employ_status = $request->employ_status;
        }
        
        if(is_null($user->employ_company)) {
            $user->employ_company = $request->employ_company;    
        }
        if(is_null($user->employ_name)) {
            $user->employ_name = $request->employ_name;    
        }
        if(is_null($user->employ_number)) {
            $user->employ_number = $request->employ_number;    
        }
        if(is_null($user->salary)) {
            $user->salary = $request->salary;
        }
        
        $checkbank = Bank::where('bankcode', $request->bankcode)->where('account_number', $request->account_number)->first();
        if($checkbank) {
            $bankid = $checkbank->id;
        } else {
            $bank = new Bank();
            $bank->user()->associate($user);
            $bank->bankcode = $request->bankcode;
            $bank->account_number = $request->account_number;
            $bank->account_name = $request->account_name;
            $bank->bankname = $request->bankname;
            $bank->save();
            
            $bankid = $bank->id;
        }
        
        $a = array(10,20,30,40);
        $random_keys = array_rand($a,2);
        // echo $a[$random_keys[0]];
        
        $rand = substr(uniqid('', true), -8);
        $rand .= substr(uniqid('', true), -5);
        $rand .= substr(uniqid('', true), -5);

        $loan = new Loan();
        $loan->amount = $request->amount;
        $loan->interest = $request->interest;
        $loan->final_amount = (int)$request->amount + (int)$request->interest;
        // $loan->credit_score = $conf;
        $loan->credit_score = $a[$random_keys[0]];
        $loan->ref = $rand;
        $loan->purpose = $request->purpose;
        $loan->bank_id = $bankid;
        $loan->status = 'pending';
        $loan->duration = $request->duration;
        
        if ($request->hasFile('idback')) {
            
                $imagePath = $request->file('idback');
                $imageName = time() . '_' . $imagePath->getClientOriginalName();
                $request->idback->storeAs('public/id_cards', $imageName);
                $user->idcard1 = "https://fvisng.com/assets/images/id_cards/{$imageName}";
         }
         if ($request->hasFile('idfront')) {
            
                $imagePath = $request->file('idfront');
                $imageName = time() . '_' . $imagePath->getClientOriginalName();
                $request->idfront->storeAs('public/id_cards', $imageName);
                $user->idcard2 = "https://fvisng.com/assets/images/id_cards/{$imageName}";
         }
           if($user->save() && $this->user->loans()->save($loan)) {
               return response()->json([
                    'success' => true
                   ]);
           } else {
               return response()->json([
                    'success' => false,
                    'error' => 'An error occured while processing your request.'
                   ]);
           }
        }
        
        public function businessloan() {
            $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'purpose' => 'required',
            'duration' => 'required',
            'interest' => 'required',
            'relativename' => 'required',
            'relativename' => 'required',
            'employ_status' => 'required',
            'bankcode' => 'required',
            'account_number' => 'required',
            'account_name' => 'required',
            'idback' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'idfront' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        if($validator->fails()) {
            return response()->json([
                'success'=> false,
                'error'=> $validator->messages()
            ]);
        }
        $user = auth()->user();
        $rechk = Loan::where('user_id', $user->id)->where('status', 'active')->first();
        if($rechk) {
            return response()->json([
                    'success' => false,
                    'error' => 'You already have an active loan.'
                ]);
        }
        $rechkk = Loan::where('user_id', $user->id)->where('status', 'overdue')->first();
        if($rechkk) {
            return response()->json([
                    'success' => false,
                    'error' => 'You already have an active loan.'
                ]);
        }
        
        if(is_null($user->relativename)) {
            $user->relativename = $request->relativename;
        }
        if(is_null($user->relativenumber)) {
            $user->relativenumber = $request->relativenumber;
        }
        if(is_null($user->employ_status)) {
            $user->employ_status = $request->employ_status;
        }
        
        if(is_null($user->employ_company)) {
            $user->employ_company = $request->employ_company;    
        }
        if(is_null($user->employ_name)) {
            $user->employ_name = $request->employ_name;    
        }
        if(is_null($user->employ_number)) {
            $user->employ_number = $request->employ_number;    
        }
        if(is_null($user->salary)) {
            $user->salary = $request->salary;
        }
        
        $checkbank = Bank::where('bankcode', $request->bankcode)->where('account_number', $request->account_number)->first();
        if($checkbank) {
            $bankid = $checkbank->id;
        } else {
            $bank = new Bank();
            $bank->user()->associate($user);
            $bank->bankcode = $request->bankcode;
            $bank->account_number = $request->account_number;
            $bank->account_name = $request->account_name;
            $bank->bankname = $request->bankname;
            $bank->save();
            
            $bankid = $bank->id;
        }
        
        $a = array(10,20,30,40);
        $random_keys = array_rand($a,2);
        // echo $a[$random_keys[0]];
        
        $rand = substr(uniqid('', true), -8);
        $rand .= substr(uniqid('', true), -5);
        $rand .= substr(uniqid('', true), -5);

        $loan = new Loan();
        $loan->amount = $request->amount;
        $loan->interest = $request->interest;
        $loan->final_amount = (int)$request->amount + (int)$request->interest;
        // $loan->credit_score = $conf;
        $loan->credit_score = $a[$random_keys[0]];
        $loan->ref = $rand;
        $loan->purpose = $request->purpose;
        $loan->bank_id = $bankid;
        $loan->status = 'pending';
        $loan->duration = $request->duration;
        $loan->type = 'business';
        $laon->tax_id = $request->tax_id;
        
        
        if ($request->hasFile('idback')) {
            
                $imagePath = $request->file('idback');
                $imageName = time() . '_' . $imagePath->getClientOriginalName();
                $request->idback->storeAs('public/id_cards', $imageName);
                $user->idcard1 = "https://fvisng.com/assets/images/id_cards/{$imageName}";
         }
         if ($request->hasFile('idfront')) {
            
                $imagePath = $request->file('idfront');
                $imageName = time() . '_' . $imagePath->getClientOriginalName();
                $request->idfront->storeAs('public/id_cards', $imageName);
                $user->idcard2 = "https://fvisng.com/assets/images/id_cards/{$imageName}";
         }
         if ($request->hasFile('utility')) {
            
                $imagePath = $request->file('utility');
                $imageName = time() . '_' . $imagePath->getClientOriginalName();
                $request->utility->storeAs('public/id_cards', $imageName);
                $user->utility = "https://fvisng.com/assets/utility_bills/{$imageName}";
         }
         if ($request->hasFile('statement')) {
            
                $imagePath = $request->file('statement');
                $imageName = time() . '_' . $imagePath->getClientOriginalName();
                $request->statement->storeAs('public/id_cards', $imageName);
                $user->statement = "https://fvisng.com/assets/statements/{$imageName}";
         }
         if ($request->hasFile('employ_letter')) {
            
                $imagePath = $request->file('employ_letter');
                $imageName = time() . '_' . $imagePath->getClientOriginalName();
                $request->employ_letter->storeAs('public/id_cards', $imageName);
                $user->employ_letter = "https://fvisng.com/assets/employment_letters/{$imageName}";
         }
           if($user->save() && $this->user->loans()->save($loan)) {
               return response()->json([
                    'success' => true
                   ]);
           } else {
               return response()->json([
                    'success' => false,
                    'error' => 'An error occured while processing your request.'
                   ]);
           }
        }
    }
          
          
      
        
        
    

