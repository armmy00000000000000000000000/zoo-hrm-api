<?php

namespace App\Http\Controllers\RecruitmentOnboarding;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Response\Response;  // import Response class ให้ถูกต้อง
use App\Models\Recruitment_Onboarding\jobs\model_jobs;



class Controller_Onboarding extends Controller
{
    protected $response;

    public function __construct()
    {
        $this->response = new Response(); 
    }

    public function searchJobs(Request $request)
    {
        $model_jobs = new model_jobs();
        $jobs = $model_jobs->search_jobs($request);

        if (empty($jobs)) {
            return $this->response->error('No jobs found', 404);
        }

        return $this->response->success($jobs, 'Jobs retrieved successfully');

    }

    public function view_jobs($id)
    {
        $model_jobs = new model_jobs();
        $job = $model_jobs->view_jobs($id);

        if (empty($job)) {
            return $this->response->error('Job not found', 404);
        }

        return $this->response->success($job, 'Job retrieved successfully');
    }

    public function list_position()
    {
     
        $model_jobs = new model_jobs();
        $positions = $model_jobs->list_position();

        if (empty($positions)) {
            return $this->response->error('No positions found', 404);
        }

        return $this->response->success($positions, 'Positions retrieved successfully');
        
    }

    public function create_job( Request $request)
    {
        $model_jobs = new model_jobs();
        $result = $model_jobs->create_job($request);

        if ($result === false) {
            return $this->response->error('Failed to create job', 500);
        }

        if ($result === 'duplicate') {
            return $this->response->error('Job title already exists', 409);
        }

        return $this->response->success($result, 'Job created successfully');
       
    }

    public function upload_jobfile(Request $request)
    {
        $model_jobs = new model_jobs();
        $result = $model_jobs->upload_jobfile($request);

        if ($result === false) {
            return $this->response->error('File upload failed', 500);
        }

        return $this->response->success($result, 'File uploaded successfully');
    }

    public function create_position(Request $request)
    {
        $model_jobs = new model_jobs();
        $result = $model_jobs->create_position($request);

        if ($result === false) {
            return $this->response->error('Failed to create position', 500);
        }

        return $this->response->success($result, 'Position created successfully');
    }

    public function update_status_jobs(Request $request, $id)
    {
        $model_jobs = new model_jobs();
        $result = $model_jobs->update_status_jobs($request, $id);

        if ($result === false) {
            return $this->response->error('Failed to update job status', 500);
        }

        return $this->response->success($result, 'Job status updated successfully');
    }
}



 

