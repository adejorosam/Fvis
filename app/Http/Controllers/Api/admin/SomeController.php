<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Loan;
use App\Bank;
use App\User;
use Validator;
use JWTAuth;
use Illuminate\Support\Str;
use App\Gallery;
use App\Galleryimage;
use Carbon\Carbon;

class SomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }
    
    public function stats ($id) {
        $status = Loan::where('status', 'pending')->get();
        $active = Loan::where('status', 'active')->get();
        $users = User::all();
        $loans = Loan::jsonPaginate($id);
        
        return response()->json([
                'success' => true,
                'numPending' => count($status),
                'numActive' => count($active),
                'users' => count($users),
                'loans' => $loans
            ]);
    }
    
    public function loanUser($id) {
        $findloaninfo = Loan::where('id', $id)->first();
        
        if($findloaninfo) {
            $loan = Loan::where('id', $id)->with('user', 'bank')->first();
            return response()->json([
                    'success' => true,
                    'data' => $loan,
                    'loan' => $findloaninfo
                ]);
        }
        return response()->json([
                'success' => false,
                'error' => 'An error occurred while fetching Loan'
            ]);
    }
    
    public function updateloanstatus($id, Request $request) {
        $loan = Loan::where('id', $id)->first();
        if($loan) {
            Loan::where('id', $id)
          ->update([
          'status' => $request['newstatus']
          ]);
          
          if($request->newstatus == 'active') {
              Loan::where('id', $id)
              ->update([
                  'approved_date' => \Carbon\Carbon::Now()
                  ]);
          }
          return response()->json([
                'success' => true,
                'data' => 'Loan Status Updated Successfully'
              ]);
        }
        return response()->json([
                'success' => false
            ]);
    }
    
    public function manageusers() {
        $users = User::orderBy('created_at', 'desc')->jsonPaginate(20);
        if($users) {
            return response()->json([
                    'success' => true,
                    'data' => $users
                ]);
        } else {
            return response()->json([
                    'success' => false,
                    'error' => "Users can't be fetched"
                ]);
        }
    }
    
    public function loans() {
        $loans = Loan::with("user")->orderBy('created_at', 'desc')->jsonPaginate(20);
        if($loans) {
            return response()->json([
                    'success' => true,
                    'data' => $loans
                ]);
        } else {
            return response()->json([
                    'success' => false,
                    'error' => "Loans can't be fetched"
                ]);
        }
    }
    
    public function galleries() {
        $galleries = Gallery::with("user")->orderBy('created_at', 'desc')->jsonPaginate(20);
        if($galleries) {
            return response()->json([
                    'success' => true,
                    'data' => $galleries
                ]);
        } else {
            return response()->json([
                    'success' => false,
                    'error' => "Galleries can't be fetched"
                ]);
        }
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
    
    public function storegallery(Request $request) {
            $slugcheck = Str::slug($request->title, '-');
            
            $gallerycheck = Gallery::where('slug', $slugcheck)->first();
            if($gallerycheck) {
                return response()->json([
                        'success' => false,
                        'error' => 'Title already exists.'
                    ]);
            }
            $user = JWTAuth::parseToken()->authenticate();
            $gallery = new Gallery();
            $gallery->title = $request->title;
            $gallery->user()->associate($user);
            $gallery->slug = $slugcheck;
            $gallery->save();
        
            foreach ($request->file('files') as $file) {
            $imagePath = $file->getClientOriginalName();
            $time = time();
            $month = date("m", $time);
            $year = date("Y", $time);
            $folder = "/".$year."/".$month."/";
            $filename = time() . ''.Str::random(5) . '' . $imagePath;
            $file->storeAs('public/gallery-img/'.$folder, $filename);
            $galleryimage = new Galleryimage();
            $galleryimage->gallery_url = "https://fvisng.com/assets/gallery-img/".$year."/".$month."/".$filename;
            $galleryimage->gallery()->associate($gallery);
            $galleryimage->save();
        }
        
        return response()->json([
                'success' => true,
                'data' => 'Data Successfully Uploaded'
            ]);
    }
    
    public function getloanoptions($options) {
        $check = Loan::where('status', $options)->with('user')->orderBy('created_at', 'desc')->jsonPaginate(20);
        if($check) {
            return response()->json([
                    'success' => true,
                    'data' => $check
                ]);
        } else {
            return response()->json([
                    'success' => false,
                    'error' => "Loans can't be fetched"
                ]);
        }
    }
    
    public function makeadmin(Request $request) {
        $user = User::where('id', $request->user_id)->first();
        
        $user->scope = 'admin';
        $user->save();
        
        return response()->json([
                'success' => true,
                'data' => 'User role updated successfully'
            ]);
    }
}
