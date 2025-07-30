<?php

namespace App\Http\Controllers\RecruitmentOnboarding;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Response\Response;  // import Response class ให้ถูกต้อง
use App\Models\Recruitment_Onboarding\interview_evaluation\model_interview_evaluation;
use Illuminate\Support\Facades\Validator;


class Controller_interview_evaluation extends Controller
{
    protected $response;

    public function __construct()
    {
        $this->response = new Response(); 
    }
    public function list_of_eligible_candidates(Request $request)
    {
        
        $model = new model_interview_evaluation();
        $result = $model->list_of_eligible_candidates($request);

        if ($result) {
            return $this->response->success($result, 'List of eligible candidates retrieved successfully.');
        } else {
            return $this->response->error('No eligible candidates found.', 404);
        }
    }   
    public function list_of_eligible_candidates_users(Request $request)
    {
        
        $model = new model_interview_evaluation();
        $result = $model->list_of_eligible_candidates_users($request);

        if ($result) {
            return $this->response->success($result, 'List of eligible candidates retrieved successfully.');
        } else {
            return $this->response->error('No eligible candidates found.', 404);
        }
    }   
    public function vidw_of_eligible_candidates_users(Request $request)
    {
        
        $model = new model_interview_evaluation();
        $result = $model->vidw_of_eligible_candidates_users($request);

        if ($result) {
            return $this->response->success($result, 'List of eligible candidates retrieved successfully.');
        } else {
            return $this->response->error('No eligible candidates found.', 404);
        }
    }   

    public function enter_your_score(Request $request)
    {


        $model = new model_interview_evaluation();
        $result = $model->enter_your_score($request);
        if ($result === true) {
            return $this->response->success($result, 'Score entered successfully.');
        } else {
            return $this->response->error('กรอกข้อมูลไม่ครบ กรุณาตรวจสอบ', 500);
        }
    }

    public function create_room(Request $request)
    {
        $model = new model_interview_evaluation();
        $result = $model->create_room($request);

        if ($result === true) {
            return $this->response->success($result, 'Room created successfully.');
        } else {
            return $this->response->error('Failed to create room.', 500);
        }
    }


       

}

 

