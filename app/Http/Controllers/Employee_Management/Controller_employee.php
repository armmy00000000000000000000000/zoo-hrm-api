<?php

namespace App\Http\Controllers\Employee_Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Response\Response;  // import Response class ให้ถูกต้อง
use App\Models\Employee_Management\employee\model_employee;
use Illuminate\Support\Facades\Validator;


class Controller_employee extends Controller
{
    protected $response;

    public function __construct()
    {
        $this->response = new Response(); 
    }
    public function list_employee(Request $request)
    {
        $model = new model_employee();
        $applications = $model->list_employee($request);

        if (empty($applications)) {
            return $this->response->error('No applications found', 404);
        }

        return $this->response->success($applications, 'Applications retrieved successfully');
    }
}
       



 

