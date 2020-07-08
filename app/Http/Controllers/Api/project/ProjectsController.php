<?php

namespace App\Http\Controllers\Api\project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use JWTAuth;
use App\Project;
use Carbon\Carbon;
use Validator;

class ProjectsController extends Controller
{
    public function listprojects() {
        $project = Project::jsonPaginate();
        if($project) {
            return response()->json([
                'success' => true,
                'data' => $project
            ]);    
        }
        return response()->json([
                'success' => false,
                'error' => 'An error occured while fetching Projects'
            ]);
        
    }
    
    public function listuserprojects() {
        $user = JWTAuth::parseToken()->authenticate();
        if($user) {
            $userproj = $user->projects()->jsonPaginate();
            return repsonse()->json([
                    'success' => true,
                    'data' => $userproj
                ]);
        }
            return response()->json([
                'success' => false,
                'error' => 'Unable to fetch user projects'
                ]);
    }
    
    public function newproject(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'category' => 'required',
            'type' => 'required',
            'budget' => 'required',
            'description' => 'required'
        ]);
        
        if($validator->fails()) {
            return response()->json([
                'success'=> false,
                'error'=> $validator->messages()
            ]);
        }
        
        $project = new Project();
        $user = JWTAuth::parseToken()->authenticate();
        $project->user()->associate($user);
        $project->name = $requrest->name;
        $project->category = $request->category;
        $project->type = $request->type;
        $project->description = $request->description;
        $project->budget = $request->budget;
        
        if ($request->hasFile('proposal')) {
            $imagePath = $request->file('proposal');
            $imageName = time() . '_' . $imagePath->getClientOriginalName();
        
          if($request->proposal->storeAs('public/proposals', $imageName)) {
              
              $project->proposal = "https://fvisng.com/assets/proposals/{$imageName}";
              $project->save();
              return response()->json([
                  'success' => true,
                  'data' => $project
                  ]);
          }
      } else {
            $project->save();          
          return response()->json([
                'success' => true,
                'data' => $project
              ]);
      }
        return response()->json([
                'success' => false,
                'error' => 'An error occured while submitting your Proposal'
            ]);
    }
}
