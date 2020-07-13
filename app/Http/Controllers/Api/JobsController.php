<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\StoreJobApplicationRequest;
use App\Job;
use App\JobApplication;
use JWTAuth;
use Illuminate\Support\Str;

class JobsController extends Controller
{

    public function __construct()
    {
        $this->middleware('forceJson');
    }

    public function listJobApplications(){
        $jobApplications = JobApplication::all();
        return response()->json([
                'success' => true,
                'data' => $jobApplications
            ]);
    }

    
    public function listJobs() {
        $jobs = Job::all();
        return response()->json([
                'success' => true,
                'data' => $jobs
            ]);
    }

    public function apply(StoreJobApplicationRequest $request){
        $user = JWTAuth::parseToken()->authenticate();
        $job = Job::findOrFail($request->job_id);
        if($job){
            $validated = $request->validated();
            $validated['job_id'] = $job->id;
            $validated['user_id'] = $user->id;
            if($validated['resume'] != null){
                $resumePath = $validated['resume'];
                $resumeName = time() . '_' . $resumePath->getClientOriginalName();
                $validated['resume'] = $resumeName;
            }
            $request->resume->storeAs('public/fv-resumes', $resumeName);
            $storedForm = JobApplications::create($validated);
        }else {
            return response()->json([
                        'success' => false,
                        'data' => 'Job not found'
                    ], 404);
        }
       

    }

    public function store(StoreJobRequest $request){
        $user = JWTAuth::parseToken()->authenticate();
        $validated = $request->validated();
        $validated['slug'] = Str::slug($validated['roleName'], '-');
        $validated['jobID'] = 'JR'.'-'. rand(100,1000);
        $validated['user_id'] = $user->id;
        $storedForm = Job::create($validated);
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

    public function getJobByID($id){
        $job = Job::with("jobApplications")->orderBy('created_at', 'desc')->get();
        if($job) {
            return response()->json([
                'success' => true,
                'data' => $job
                ]);
        } 
        else {
            return response()->json([
                    'success' => false
                ]);
        }
    }

    public function getSingleJob($slug){
        $job = Job::where('slug', $slug)->first(); 
        if($job) {
            return response()->json([
                'success' => true,
                'data' => $job
                ]);
        } else {
            return response()->json([
                    'success' => false
                ]);
        }
    }

    public function updateJob(StoreJobRequest $request) {
        $job = Job::find($request->job_id);
        if($job) {
            $validated = $request->validated();
            $updatedForm = $job->update($validated);
            if($updatedForm) {
                return response()->json([
                        'success' => true,
                        'data' => 'Job Updated Successfully'
                    ]);
            } else {
                return response()->json([
                        'success' => false,
                        'data' => 'An error occured while trying to update job'
                    ]);
            }
            
        }else {
            return response()->json([
                        'success' => false,
                        'data' => 'Job not found'
                    ], 404);
        }
    }

    public function destroy(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();   
        $job = Job::find($request->job_id);
        if($job->user_id != $user->id)
        {
            return response()->json(["status" =>false,"message" => "Unauthorized action"], 401);
        }
        if($job){
            if($job->delete()) {
                return response([
                    'status' => true,
                    'data'   => null,
                    'message' => 'Job deleted successfully'
                ]);
            } 
            else 
            {
                return response([
                    'status' => false,
                    'data' => null,
                    'message' => 'Update Failed!'
                ]);
            }
        }else{
            return response()->json([
                        'success' => false,
                        'data' => 'Job not found'
                    ], 404);
        }
    }
}
