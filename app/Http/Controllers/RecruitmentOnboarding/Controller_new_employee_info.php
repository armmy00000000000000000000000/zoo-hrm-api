<?php

namespace App\Http\Controllers\RecruitmentOnboarding;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Response\Response;  // import Response class ให้ถูกต้อง
use App\Models\Recruitment_Onboarding\new_employee_info\model_new_employee_info;
use Illuminate\Support\Facades\Validator;


class Controller_new_employee_info extends Controller
{
    protected $response;

    public function __construct()
    {
        $this->response = new Response(); 
    }
    public function the_names_have_been_selected(Request $request)
    {
        
        $model = new model_new_employee_info();
        $result = $model->the_names_have_been_selected($request);

        if ($result) {
            return $this->response->success($result, 'List of eligible candidates retrieved successfully.');
        } else {
            return $this->response->error('No eligible candidates found.', 404);
        }
    }   
    

        public function viewthe_names_have_been_selected(Request $request)
    {
        
        $model = new model_new_employee_info();
        $result = $model->viewthe_names_have_been_selected($request);

        if ($result) {
            return $this->response->success($result, 'List of eligible candidates retrieved successfully.');
        } else {
            return $this->response->error('No eligible candidates found.', 404);
        }
    }   

    public function register_new_employee(Request $request)
    {
        $model = new model_new_employee_info();
        $result = $model->register_new_employee($request);

        if ($result) {
            return $this->response->success($result, 'New employee registered successfully.');
        } else {
            return $this->response->error('Failed to register new employee.', 500);
        }
    }
    public function getNewEmployee()
    {
        $model = new model_new_employee_info();
        $result = $model->getNewEmployee();

        if ($result) {
            return $this->response->success($result, 'New employee registered successfully.');
        } else {
            return $this->response->error('Failed to register new employee.', 500);
        }
    }
    public function getNewEmployeeInfo($id)
    {
        $model = new model_new_employee_info();
        $result = $model->getNewEmployeeInfo($id);

        if ($result) {
            return $this->response->success($result, 'New employee registered successfully.');
        } else {
            return $this->response->error('Failed to register new employee.', 500);
        }
    }

       

}

 

