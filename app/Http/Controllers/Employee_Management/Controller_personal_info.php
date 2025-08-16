<?php

namespace App\Http\Controllers\Employee_Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Response\Response;  // import Response class ให้ถูกต้อง
use App\Models\Employee_Management\personal_info\model_personal_info;
use Illuminate\Support\Facades\Validator;


class Controller_personal_info extends Controller
{
    protected $response;

    public function __construct()
    {
        $this->response = new Response(); 
    }
  public function view_personal_info($id)
    {
        $model = new model_personal_info();
        $applicant = $model->view_personal_info($id);

        if (empty($applicant)) {
            return $this->response->error('No applicant found', 404);
        }

        return $this->response->success($applicant, 'Applicant retrieved successfully');
    }

    public function update_personal_info(Request $request)
    {
        $data = $request->all();

        // Validate the incoming request data
        $validator = Validator::make($data, [
            'application_id' => 'required|integer',
            // Add other validation rules as needed
        ]);

        if ($validator->fails()) {
            return $this->response->error($validator->errors(), 422);
        }

        $model = new model_personal_info();
        $result = $model->update_personal_info($data);

        if ($result === true) {
            return $this->response->success([], 'Personal info updated successfully');
        } else {
            return $this->response->error('Failed to update personal info', 500);
        }
    }
}
       



 

