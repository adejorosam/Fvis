<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;
use App\User;
use Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Auth\Access\AuthorizationException;

class AuthController extends Controller
{
    public function login(Request $request) {
        $input = $request->only('email', 'password');
        $token = null;

        if (!$token = JWTAuth::attempt($input)) {
        return response()->json([
            'success' => false,
            'error' => 'Incorrect Email or PIN'
        ], 422);
    }

        return response()->json([
            'success' => true,
            'token' => $token
        ]);
    }
    
    public function register(Request $request) {
        $credentials = $request->only('first_name', 'last_name', 'email', 'phone_number', 'password', 'password_confirmation');
        
        $rules = [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'phone_number' => 'required|max:15|unique:users',
            'password' => 'required|confirmed'
        ];

        $validator = Validator::make($credentials, $rules);
        if($validator->fails()) {
            return response()->json(['success'=> false, 'error'=> $validator->messages()]);
        }
        $ref = $this->random_strings(6);
        $recheck = User::where('ref_id', $ref)->first();
        if($recheck) {
            $ref = $this->random_strings(6);
            if(User::where('ref_id', $ref)->first()) {
                $ref = $this->random_strings(6);
            }
        }
        // $scope = array('user');
        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        if($request->has('ref_id')) {
            $checking = User::where('referral', $request->ref_id);
            if($checking) {
                $checking->wallet += 100;
            }
        $user->ref_id = $checking->id;
        }
        $user->phone_number = $request->phone_number;
        $user->password = bcrypt($request->password);
        $user->gender = $request->gender;
        $user->scope = 'user';
        $user->referral = $ref;
        $user->save();
        
        $user->sendEmailVerificationNotification();

        return response()->json([
            'success'   =>  true,
            'data'      =>  $user
        ], 200);
    }
    
    public function user(Request $request)
    {
        try {

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        // return response()->json([
        //     'success' => true,
        //     'user' => $user
        //     ]);
        $scope = array($user->scope);
        $me = array(
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone_number' => $user->phone_number,
            'gender' => $user->gender,
            'dob' => $user->dob,
            'nationality' => $user->nationality,
            'state_of_origin' => $user->state_of_origin,
            'lga_of_origin' => $user->lga_of_origin,
            'lga_of_residence' => $user->lga_of_residence,
            'state_of_residence' => $user->state_of_residence,
            'marital_status' => $user->marital_status,
            'residential_address' => $user->residential_address,
            'scope' => $scope,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'profile_img' => $user->profile_img,
            'employ_status' => $user->employ_status,
            'wallet' => $user->wallet,
            'member_id' => $user->member_id
        );
        return response()->json([
            'success' => true,
            'user' => $me
            ]);
    }
    
    public function logout(Request $request) {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json([
                'success' => true,
                'message'=> "You have successfully logged out."
                ], 200);
        } catch (JWTException $e) {
            return response()->json(['success' => false, 'error' => 'Failed to logout, please try again.'], 401);
        }
    }
    
    public function refresh(Request $request) {
        try {
            $newToken = auth()->refresh();
        } catch(Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                    'error' => $e->getMessage()
                ], 401);
        }
        return response()->json([
            'refresh_token' => $newToken
            ]);
    }
    
    function random_strings($length_of_string) 
    { 
        $str_result = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz'; 
        return substr(str_shuffle($str_result), 0, $length_of_string); 
    } 
}
