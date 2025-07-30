<?php

namespace App\Http\Controllers\RecruitmentOnboarding;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Response\Response;  // import Response class ให้ถูกต้อง
use App\Models\Recruitment_Onboarding\application_tracking\model_application_tracking;
use Illuminate\Support\Facades\Validator;


class Controller_application_tracking extends Controller
{
    protected $response;

    public function __construct()
    {
        $this->response = new Response(); 
    }

    public function create_application(Request $request)
    {
        $model = new model_application_tracking();
        $result = $model->register_applicationss($request);

        if ($result === 'duplicate') {
            return $this->response->error('มีใบสมัครงานนี้และบัตรประจำตัวแล้ว', 409);
        } elseif ($result === false) {
            return $this->response->error('เกิดข้อมูลผิดผลาดกรุณาลองใหม่');
        }else{
            return $this->response->success($result,'บันทึกข้อมูลสำเร็จ',200);
        }
    }

    public function education_history(Request $request) {
        $model = new model_application_tracking();
        $result = $model->education_history($request);

       if ($result === false) {
            return $this->response->error('ข้อมูลไม่ครบถ้วน',400);
        }else{
            return $this->response->success($result,'บันทึกข้อมูลสำเร็จ',200);
        }
    }
    public function training(Request $request) {
        $model = new model_application_tracking();
        $result = $model->training($request);

       if ($result === false) {
            return $this->response->error('ข้อมูลไม่ครบถ้วน',400);
        }else{
            return $this->response->success($result,'บันทึกข้อมูลสำเร็จ',200);
        }
    }
    public function foreign_language_skills(Request $request) {
        $model = new model_application_tracking();
        $result = $model->foreign_language_skills($request);

       if ($result === false) {
            return $this->response->error('ข้อมูลไม่ครบถ้วน',400);
        }else{
            return $this->response->success($result,'บันทึกข้อมูลสำเร็จ',200);
        }
    }
    public function work_experience(Request $request) {
        $model = new model_application_tracking();
        $result = $model->work_experience($request);

       if ($result === false) {
            return $this->response->error('ข้อมูลไม่ครบถ้วน',400);
        }else{
            return $this->response->success($result,'บันทึกข้อมูลสำเร็จ',200);
        }
    }
    public function typing_skills(Request $request) {
        $model = new model_application_tracking();
        $result = $model->typing_skills($request);

       if ($result === false) {
            return $this->response->error('ข้อมูลไม่ครบถ้วน',400);
        }else{
            return $this->response->success($result,'บันทึกข้อมูลสำเร็จ',200);
        }
    }
    public function driving_skills(Request $request) {
        $model = new model_application_tracking();
        $result = $model->driving_skills($request);

       if ($result === false) {
            return $this->response->error('ข้อมูลไม่ครบถ้วน',400);
        }else{
            return $this->response->success($result,'บันทึกข้อมูลสำเร็จ',200);
        }
    }
    public function health_conditions(Request $request) {
        $model = new model_application_tracking();
        $result = $model->health_conditions($request);

       if ($result === false) {
            return $this->response->error('ข้อมูลไม่ครบถ้วน',400);
        }else{
            return $this->response->success($result,'บันทึกข้อมูลสำเร็จ',200);
        }
    }
    public function family_info(Request $request) {
        $model = new model_application_tracking();
        $result = $model->family_info($request);

       if ($result === false) {
            return $this->response->error('ข้อมูลไม่ครบถ้วน',400);
        }else{
            return $this->response->success($result,'บันทึกข้อมูลสำเร็จ',200);
        }
    }

public function uploadDocument(Request $request)
{
    $application_id = $request->input('application_id');
    $document_type = $request->input('document_type');

    if (!$request->hasFile('file')) {
        return response()->json(['status' => false, 'message' => 'ไม่พบไฟล์ใน request'], 400);
    }

    $files = $request->file('file');

    if (!is_array($files)) {
        $files = [$files]; // แปลงเป็น array กรณีอัปโหลดไฟล์เดียว
    }

    $model = new model_application_tracking();
    $results = [];

    foreach ($files as $file) {
        $result = $model->document_uploads($file, $application_id, $document_type);
        $results[] = $result;
        if (!$result['status']) {
            // หยุดอัปโหลดถ้าไฟล์ไหนล้มเหลว
            break;
        }
    }

    return response()->json($results);
}

    public function list_the_application(Request $request)
    {
        $model = new model_application_tracking();
        $jobs = $model->list_the_application($request);

        if (empty($jobs)) {
            return $this->response->error('No jobs found', 404);
        }

        return $this->response->success($jobs, 'Jobs retrieved successfully');

    }

    public function list_of_job_applicants(Request $request)
    {
        $model = new model_application_tracking();
        $applications = $model->list_of_job_applicants($request);

        if (empty($applications)) {
            return $this->response->error('No applications found', 404);
        }

        return $this->response->success($applications, 'Applications retrieved successfully');
    }

    public function view_of_job_applicants($id)
    {
        $model = new model_application_tracking();
        $applicant = $model->view_of_job_applicants($id);

        if (empty($applicant)) {
            return $this->response->error('No applicant found', 404);
        }

        return $this->response->success($applicant, 'Applicant retrieved successfully');
    }
    public function find_an_application($id)
    {
        $model = new model_application_tracking();
        $applicant = $model->find_an_application($id);

        if (empty($applicant)) {
            return $this->response->error('No applicant found', 404);
        }

        return $this->response->success($applicant, 'Applicant retrieved successfully');
    }

    public function status_applications(Request $request)
    {
        $model = new model_application_tracking();
        $result = $model->status_applications($request);

        if ($result === false) {
            return $this->response->error('ไม่สามารถอัปเดตสถานะใบสมัครได้', 400);
        } else {
            return $this->response->success($result, 'อัปเดตสถานะใบสมัครสำเร็จ', 200);
        }
    }
}

       



 

