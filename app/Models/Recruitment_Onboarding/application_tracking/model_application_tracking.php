<?php

namespace App\Models\Recruitment_Onboarding\application_tracking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class model_application_tracking extends Model
{

//  public function register_applicationss($request)
//     {

//         $data_check = [
//             'job_id' => $request->input('job_id'),
//             'job_position_id' => $request->input('job_position_id'),
//             'id_card' => $request->input('id_card'),
//         ];

//         $check = DB::table('applications')
//             ->where($data_check)
//             ->first();
        
//         if ($check) {
//             return 'duplicate'; // ข้อมูลซ้ำ
//         }   
//         if (!$request->hasFile('photo_file')) {
//             return response()->json(['message' => 'ไม่พบไฟล์ที่อัปโหลด'], 400);
//         }
//         if (!$request->hasFile('resume_file')) {
//             return response()->json(['message' => 'ไม่พบไฟล์ที่อัปโหลด'], 400);
//         }

//         // ย้ายไฟล์ทั้ง photo และ resume
//         $photo_file = $request->file('photo_file');
//         $resume_file = $request->file('resume_file');

//         // ตรวจสอบประเภท
//         $allowed_photo = ['jpg', 'jpeg', 'png', 'pdf'];
//         $photo_ext = strtolower($photo_file->getClientOriginalExtension());
//         $resume_ext = strtolower($resume_file->getClientOriginalExtension());

//         if (!in_array($photo_ext, $allowed_photo) || !in_array($resume_ext, ['pdf', 'doc', 'docx'])) {
//             return response()->json(['message' => 'ประเภทไฟล์ไม่ถูกต้อง'], 422);
//         }

//         $filename_photo = time() . '_' . uniqid() . '.' . $photo_ext;
//         $filename_resume = time() . '_' . uniqid() . '.' . $resume_ext;

//         $uploadPath = base_path('public/jobfile');
//         if (!file_exists($uploadPath)) mkdir($uploadPath, 0755, true);

//         $photo_file->move($uploadPath, $filename_photo);
//         $resume_file->move($uploadPath, $filename_resume);

//         $url_photo_file = url('jobfile/' . $filename_photo);
//         $url_resume_file = url('jobfile/' . $filename_resume);




//         $data = [
//             'job_id' => $request->input('job_id'),
//             'job_position_id' => $request->input('job_position_id'),
//             'applicant_name' => $request->input('applicant_name'),
//             'id_card' => $request->input('id_card'),
//             'date_idcard_leave_when' => $request->input('date_idcard_leave_when'),
//             'date_idcard_expire' => $request->input('date_idcard_expire'),
//             'email' => $request->input('email'),
//             'phone' => $request->input('phone'),
//             'address' => $request->input('address'),
//             'education_level' => $request->input('education_level'),
//             'experience' => $request->input('experience'),
//             'resume_file' => $url_resume_file,
//             'prefix_th' => $request->input('prefix_th'),
//             'prefix_en' => $request->input('prefix_en'),
//             'first_name_en' => $request->input('first_name_en'),
//             'last_name_en' => $request->input('last_name_en'),
//             'birth_date' => $request->input('birth_date'),
//             'religion' => $request->input('religion'),
//             'nationality' => $request->input('nationality'),
//             'ethnicity' => $request->input('ethnicity'),
//             'height_cm' => $request->input('height_cm'),
//             'weight_kg' => $request->input('weight_kg'),
//             'marital_status' => $request->input('marital_status'),
//             'military_status' => $request->input('military_status'),
//             'has_been_jailed' => $request->input('has_been_jailed'),
//             'ever_fired' => $request->input('ever_fired'),
//             'email_personal' => $request->input('email_personal'),
//             'line_id' => $request->input('line_id'),
//             'photo_file' => $url_photo_file,
//             'address_permanent' => $request->input('address_permanent'),
//             'address_current' => $request->input('address_current'),
//         ];

//         $appllicationId = DB::table('applications')->insertGetId($data);
//         if (!$appllicationId) {
//             return false;
//         }else {
//   // ถ้าสมรส -> เพิ่มข้อมูล spouse
//     if ($appllicationId && $request->input('marital_status') === 'สมรส') {
//         $dataspouses = [
//             'application_id' => $appllicationId,
//             'prefix' => $request->input('spouse_prefix'),
//             'first_name' => $request->input('spouse_first_name'),
//             'last_name' => $request->input('spouse_last_name'),
//             'nationality' => $request->input('spouse_nationality'),
//             'ethnicity' => $request->input('spouse_ethnicity'),
//             'religion' => $request->input('spouse_religion'),
//             'occupation' => $request->input('spouse_occupation'),
//             'position' => $request->input('spouse_position'),
//             'workplace' => $request->input('spouse_workplace'),
//             'phone' => $request->input('spouse_phone'),
//             'address_no' => $request->input('spouse_address_no'),
//             'soi' => $request->input('spouse_soi'),
//             'road' => $request->input('spouse_road'),
//             'province_id' => $request->input('spouse_province_id'),
//             'district_id' => $request->input('spouse_district_id'),
//             'subdistrict_id' => $request->input('spouse_subdistrict_id'),
//             'children_count' => $request->input('children_count'),
//             'children_male' => $request->input('children_male'),
//             'children_female' => $request->input('children_female'),
//         ];

//         DB::table('spouses')->insert($dataspouses);
//     }
//             return true;
//         }   
//     }

    public function register_applicationss($request)
    {
        // ตรวจสอบข้อมูลซ้ำ
        $data_check = [
            'job_id' => $request->input('job_id'),
            'job_position_id' => $request->input('job_position_id'),
            'id_card' => $request->input('id_card'),
        ];

        $check = DB::table('applications')->where($data_check)->first();
        if ($check) {
            return 'duplicate';
        }

        // ตรวจสอบไฟล์ที่อัปโหลด
        if (!$request->hasFile('photo_file') || !$request->hasFile('resume_file')) {
            return response()->json(['message' => 'ไม่พบไฟล์ที่อัปโหลด'], 400);
        }

        // ตรวจสอบประเภทไฟล์
        $photo_file = $request->file('photo_file');
        $resume_file = $request->file('resume_file');

        $allowed_photo = ['jpg', 'jpeg', 'png', 'pdf'];
        $photo_ext = strtolower($photo_file->getClientOriginalExtension());
        $resume_ext = strtolower($resume_file->getClientOriginalExtension());

        if (!in_array($photo_ext, $allowed_photo) || !in_array($resume_ext, ['pdf', 'doc', 'docx'])) {
            return response()->json(['message' => 'ประเภทไฟล์ไม่ถูกต้อง'], 422);
        }

        // อัปโหลดไฟล์
        $filename_photo = time() . '_' . uniqid() . '.' . $photo_ext;
        $filename_resume = time() . '_' . uniqid() . '.' . $resume_ext;

        $uploadPath = base_path('public/jobfile');
        if (!file_exists($uploadPath)) mkdir($uploadPath, 0755, true);

        $photo_file->move($uploadPath, $filename_photo);
        $resume_file->move($uploadPath, $filename_resume);

        $url_photo_file = url('jobfile/' . $filename_photo);
        $url_resume_file = url('jobfile/' . $filename_resume);
        // ปีปัจจุบันใน พ.ศ.
        $thai_year = date('Y') + 543;

        // ดึงเลขใบสมัครล่าสุดของปีนี้
        $last = DB::table('applications')
            ->where('application_number', 'LIKE', '%/' . $thai_year)
            ->orderByDesc('application_id')
            ->value('application_number');

        // คำนวณเลขที่ถัดไป
        if ($last) {
            // แยกส่วนหน้า เช่น 0005 จาก "0005/2568"
            $last_number = intval(explode('/', $last)[0]);
            $new_number = str_pad($last_number + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $new_number = '0001';
        }

        $application_number = $new_number . '/' . $thai_year;
        // บันทึกข้อมูลผู้สมัคร
        $data = [
            'application_number' => $application_number,
            'job_id' => $request->input('job_id'),
            'job_position_id' => $request->input('job_position_id'),
            'applicant_name' => $request->input('applicant_name'),
            'id_card' => $request->input('id_card'),
            'date_idcard_leave_when' => $request->input('date_idcard_leave_when'),
            'date_idcard_expire' => $request->input('date_idcard_expire'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'education_level' => $request->input('education_level'),
            'experience' => $request->input('experience'),
            'resume_file' => $url_resume_file,
            'prefix_th' => $request->input('prefix_th'),
            'prefix_en' => $request->input('prefix_en'),
            'first_name_en' => $request->input('first_name_en'),
            'last_name_en' => $request->input('last_name_en'),
            'birth_date' => $request->input('birth_date'),
            'religion' => $request->input('religion'),
            'nationality' => $request->input('nationality'),
            'ethnicity' => $request->input('ethnicity'),
            'height_cm' => $request->input('height_cm'),
            'weight_kg' => $request->input('weight_kg'),
            'marital_status' => $request->input('marital_status'),
            'military_status' => $request->input('military_status'),
            'has_been_jailed' => $request->input('has_been_jailed'),
            'ever_fired' => $request->input('ever_fired'),
            'email_personal' => $request->input('email_personal'),
            'line_id' => $request->input('line_id'),
            'photo_file' => $url_photo_file,
            'address_permanent' => $request->input('address_permanent'),
            'address_current' => $request->input('address_current'),
            'talent' => $request->input('talent'),
            'hobby' => $request->input('hobby'),
        ];

                // เพิ่มเฉพาะกรณีถูกจำคุกมาก่อน
            if ($request->input('has_been_jailed') == 1) {
                $data['has_been_detall'] = $request->input('has_been_detall');
            }
                $applicationId = DB::table('applications')->insertGetId($data);

        // ถ้าสมรส -> เพิ่มข้อมูล spouse
        if ($applicationId && $request->input('marital_status') === 'สมรส') {
            $dataspouses = [
                'application_id' => $applicationId,
                'prefix' => $request->input('spouse_prefix'),
                'first_name' => $request->input('spouse_first_name'),
                'last_name' => $request->input('spouse_last_name'),
                'nationality' => $request->input('spouse_nationality'),
                'ethnicity' => $request->input('spouse_ethnicity'),
                'religion' => $request->input('spouse_religion'),
                'occupation' => $request->input('spouse_occupation'),
                'position' => $request->input('spouse_position'),
                'workplace' => $request->input('spouse_workplace'),
                'phone' => $request->input('spouse_phone'),
                'address_no' => $request->input('spouse_address_no'),
                'soi' => $request->input('spouse_soi'),
                'road' => $request->input('spouse_road'),
                'province_id' => $request->input('spouse_province_id'),
                'district_id' => $request->input('spouse_district_id'),
                'subdistrict_id' => $request->input('spouse_subdistrict_id'),
                'children_count' => $request->input('children_count'),
                'children_male' => $request->input('children_male'),
                'children_female' => $request->input('children_female'),
            ];

            DB::table('spouses')->insert($dataspouses);
        }

        return $applicationId;
    }


    /// เก็บข้อมูลสมรส กรณี สมรส

    /// เก็บประวัติการศึกษาผู้สมัคร บันทึกได้มากกว่า 1 ข้อมูล
    public function education_history($request)
    {
        $application_id = $request->input('application_id');
        $education_datas = $request->input('education_history');

        if (!is_numeric($application_id) || !is_array($education_datas)) {
            return false;
        }

        $insertData = [];

        foreach ($education_datas as $education_data) {
            $insertData[] = [
                'application_id' => $application_id,
                'level'          => $education_data['level'] ?? null,
                'institution'    => $education_data['institution'] ?? null,
                'major'          => $education_data['major'] ?? null,
                'degree'         => $education_data['degree'] ?? null,
                'start_year'     => $education_data['start_year'] ?? null,
                'end_year'       => $education_data['end_year'] ?? null
            ];
        }

        DB::table('education_history')->insert($insertData);

        return true;
    }

    /// บันทึกประวัติการอบรมหรือศึกษาดูงานของผู้สมัคร บันทึกได้มากกว่า 1 ข้อมูล
    public function training($request)
    {
        $application_id = $request->input('application_id');
        $trainings = $request->input('training');

        if (!is_numeric($application_id) || !is_array($trainings)) {
            return false;
        }

        $insertData = [];

        foreach ($trainings as $training) {
            $insertData[] = [
                'application_id' => $application_id,
                'place'          => $training['place'] ?? null,
                'start_date'    => $training['start_date'] ?? null,
                'end_date'          => $training['end_date'] ?? null,
                'subject'         => $training['subject'] ?? null,
                'type'     => $training['type'] ?? null,
            ];
        }

        DB::table('training')->insert($insertData);

        return true;
    }


   
    public function foreign_language_skills($request)
    {
        $application_id = $request->input('application_id');
        $foreign_language_skills = $request->input('foreign_language_skills');

        if (!is_numeric($application_id) || !is_array($foreign_language_skills)) {
            return false;
        }

        $insertData = [];

        foreach ($foreign_language_skills as $foreign_language_skill) {
            $insertData[] = [
                'application_id' => $application_id,
                'language'          => $foreign_language_skill['language'] ?? null,
                'listening'    => $foreign_language_skill['listening'] ?? null,
                'speaking'          => $foreign_language_skill['speaking'] ?? null,
                'reading'         => $foreign_language_skill['reading'] ?? null,
                'writing'     => $foreign_language_skill['writing'] ?? null,
            ];
        }

        DB::table('foreign_language_skills')->insert($insertData);

        return true;
    }
    /// บันทึกข้อมูลประสบการณ์ทำงานย้อนหลังของผู้สมัคร บันทึกได้มากกว่า 1 ข้อมูล
    public function work_experience($request)
    {
               $application_id = $request->input('application_id');
        $work_experiences = $request->input('work_experience');

        if (!is_numeric($application_id) || !is_array($work_experiences)) {
            return false;
        }

        $insertData = [];

        foreach ($work_experiences as $work_experience) {
            $insertData[] = [
                'application_id' => $application_id,
                'company_name'          => $work_experience['company_name'] ?? null,
                'position'    => $work_experience['position'] ?? null,
                'job_description'          => $work_experience['job_description'] ?? null,
                'salary'         => $work_experience['salary'] ?? null,
                'start_date'     => $work_experience['start_date'] ?? null,
                'end_date'     => $work_experience['end_date'] ?? null,
            ];
        }

        DB::table('work_experience')->insert($insertData);

        return true;
    }

    public function typing_skills($request)
    {
        $application_id = $request->input('application_id');
        $typing_skillss = $request->input('typing_skills');

        if (!is_numeric($application_id) || !is_array($typing_skillss)) {
            return false;
        }

        $insertData = [];

        foreach ($typing_skillss as $typing_skills) {
            $insertData[] = [
                'application_id' => $application_id,
                'language'          => $typing_skills['language'] ?? null,
                'typing_speed'    => $typing_skills['typing_speed'] ?? null,
            ];
        }

        DB::table('typing_skills')->insert($insertData);

        return true;
    }
    public function driving_skills($request)
    {
        $application_id = $request->input('application_id');
        $driving_skills = $request->input('driving_skills');

        if (!is_numeric($application_id) || !is_array($driving_skills)) {
            return false;
        }

        $insertData = [];

        foreach ($driving_skills as $driving_skill) {
            $insertData[] = [
                'application_id' => $application_id,
                'vehicle_type'  => $driving_skill['vehicle_type'] ?? null,
                'can_drive'    => $driving_skill['can_drive'] ?? null,
                'has_license'    => $driving_skill['has_license'] ?? null,
            ];
        }

        DB::table('driving_skills')->insert($insertData);

        return true;
    }

    public function health_conditions($request)
    {
        $application_id = $request->input('application_id');
        $health_conditions = $request->input('health_conditions');

        if (!is_numeric($application_id) || !is_array($health_conditions)) {
            return false;
        }

        $insertData = [];

        foreach ($health_conditions as $health_condition) {
            $insertData[] = [
                'application_id' => $application_id,
                'is_healthy'  => $health_condition['is_healthy'] ?? null,
                'under_treatment'    => $health_condition['under_treatment'] ?? null,
                'recovered_disease'    => $health_condition['recovered_disease'] ?? null,
            ];
        }

        DB::table('health_conditions')->insert($insertData);

        return true;
    }
public function family_info($request)
{
    $application_id = $request->input('application_id');

    if (!is_numeric($application_id)) {
        return false;
    }

    $insertData = [
        'application_id'       => $application_id,

        // ข้อมูลบิดา
        'father_first_name'    => $request->input('father_first_name'),
        'father_last_name'     => $request->input('father_last_name'),
        'father_race'          => $request->input('father_race', 'ไทย'),
        'father_nationality'   => $request->input('father_nationality', 'ไทย'),
        'father_religion'      => $request->input('father_religion'),
        'father_occupation'    => $request->input('father_occupation'),
        'father_position'      => $request->input('father_position'),
        'father_workplace'     => $request->input('father_workplace'),
        'father_phone'         => $request->input('father_phone'),

        // ข้อมูลมารดา
        'mother_first_name'    => $request->input('mother_first_name'),
        'mother_last_name'     => $request->input('mother_last_name'),
        'mother_race'          => $request->input('mother_race', 'ไทย'),
        'mother_nationality'   => $request->input('mother_nationality', 'ไทย'),
        'mother_religion'      => $request->input('mother_religion'),
        'mother_occupation'    => $request->input('mother_occupation'),
        'mother_position'      => $request->input('mother_position'),
        'mother_workplace'     => $request->input('mother_workplace'),
        'mother_phone'         => $request->input('mother_phone'),
    ];

    // บันทึกลงฐานข้อมูล
    DB::table('family_info')->insert($insertData);

    return true;
}


    /// เก็บรายการเอกสารแนบที่ผู้สมัครอัปโหลด (เช่น ใบปริญญา บัตรประชาชน ทะเบียนบ้าน ฯลฯ) บันทึกได้มากกว่า 1 ข้อมูล
public function document_uploads($file, $application_id, $document_type)
{
    // อนุญาตนามสกุลเพิ่ม: jpg, jpeg, png, pdf
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
    $maxFileSize = 5 * 1024 * 1024; // 5 MB

    $extension = strtolower($file->getClientOriginalExtension());

    if (!in_array($extension, $allowedExtensions)) {
        return ['status' => false, 'message' => 'อนุญาตเฉพาะไฟล์ JPG, JPEG, PNG และ PDF เท่านั้น'];
    }

    if ($file->getSize() > $maxFileSize) {
        return ['status' => false, 'message' => 'ขนาดไฟล์ต้องไม่เกิน 5MB'];
    }

    // ตรวจสอบจำนวนเอกสารที่อัปโหลดแล้ว
    $count = DB::table('document_uploads')
        ->where('application_id', $application_id)
        ->where('document_type', $document_type)
        ->count();

    if ($document_type === 'ปริญญาบัตร') {
        if ($count >= 10) {
            return ['status' => false, 'message' => 'สามารถอัปโหลดปริญญาบัตรได้สูงสุด 10 ใบ'];
        }
    } else {
        // สำหรับประเภทอื่นๆ อนุญาตแค่ 1 ไฟล์
        if ($count >= 1) {
            return ['status' => false, 'message' => 'เอกสารประเภทนี้สามารถอัปโหลดได้เพียง 1 ใบเท่านั้น'];
        }
    }

    // สร้างชื่อไฟล์ไม่ซ้ำ
    $fileName = uniqid() . '.' . $extension;
    $uploadPath = base_path('public/documents');

    if (!file_exists($uploadPath)) {
        mkdir($uploadPath, 0777, true); // สร้างโฟลเดอร์ถ้ายังไม่มี
    }

    $file->move($uploadPath, $fileName);
  $url = url('documents/' . $fileName);
    // บันทึกลงฐานข้อมูล
    DB::table('document_uploads')->insert([
        'application_id' => $application_id,
        'document_type' => $document_type,
        'file_name' => $fileName,
        'file_path' => $url
    ]);

    return ['status' => true, 'message' => 'อัปโหลดเรียบร้อยแล้ว'];
}

public function list_the_application($request){
    $job_title = trim($request->input('job_title', ''));
    $agency_id = $request->input('agency_id', null);
    $job_positions_input = trim($request->input('job_positions', ''));
    $search_type = $request->input('search_type', 'and'); // 'and' หรือ 'or'

    $params = [];
    $query = "SELECT job.*, agency.name 
            FROM job 
            JOIN agency ON job.agency_id = agency.id 
            WHERE 1=1";

    // เงื่อนไข job_title
    if ($job_title !== '') {
        $query .= " AND job.job_title LIKE :like_job_title";
        $params['like_job_title'] = '%' . $job_title . '%';
        $params['exact_job_title'] = $job_title;
        $params['start_job_title'] = $job_title . '%';
        $params['like_job_title2'] = '%' . $job_title . '%';
    }

    // เงื่อนไข agency
    if (!empty($agency_id)) {
        $query .= " AND job.agency_id = :agency_id";
        $params['agency_id'] = $agency_id;
    }

    // เงื่อนไขตำแหน่ง
    if ($job_positions_input !== '') {
        $params['like_position'] = '%' . $job_positions_input . '%';

        if ($search_type === 'and' && $job_title !== '') {
            $query .= " AND EXISTS (
                SELECT 1 FROM job_positions jp
                JOIN position p ON jp.position_code = p.position_id
                WHERE jp.job_id = job.job_id
                AND p.position_name LIKE :like_position
            )";
        } elseif ($search_type === 'or') {
            if ($job_title !== '') {
                $query .= " OR EXISTS (
                    SELECT 1 FROM job_positions jp
                    JOIN position p ON jp.position_code = p.position_id
                    WHERE jp.job_id = job.job_id
                    AND p.position_name LIKE :like_position
                )";
            } else {
                $query .= " AND EXISTS (
                    SELECT 1 FROM job_positions jp
                    JOIN position p ON jp.position_code = p.position_id
                    WHERE jp.job_id = job.job_id
                    AND p.position_name LIKE :like_position
                )";
            }
        }
    }

    // จัดลำดับตาม job_title ถ้ามี
    if ($job_title !== '') {
        $query .= " ORDER BY 
            CASE 
                WHEN job.job_title = :exact_job_title THEN 1
                WHEN job.job_title LIKE :start_job_title THEN 2
                WHEN job.job_title LIKE :like_job_title2 THEN 3
                ELSE 4
            END";
    } else {
        $query .= " ORDER BY job.job_id DESC";
    }

    $jobs = DB::select($query, $params);

    if (empty($jobs)) return [];

    foreach ($jobs as &$job) {
        $job->agency_name = $job->name;
        unset($job->name);

        $match_score = 0;

        if ($job_title !== '') {
            if ($job->job_title === $job_title) {
                $match_score += 100;
            } elseif (str_starts_with($job->job_title, $job_title)) {
                $match_score += 90;
            } elseif (stripos($job->job_title, $job_title) !== false) {
                $match_score += 70;
            }
        }

        // ดึงตำแหน่ง
        if ($job_positions_input === '') {
            $positions = DB::select("
                SELECT 
                    jp.position_code,
                    jp.job_position_id,
                    jp.level,
                    jp.qualification,
                    jp.salary,
                    jp.experience,
                    jp.status,
                    d.department_name,
                    p.position_name,
                    (
                        SELECT COUNT(*) 
                        FROM applications a 
                        WHERE a.job_id = jp.job_id 
                        AND a.job_position_id = jp.job_position_id
                    ) as application_count
                FROM job_positions jp
                JOIN department d ON jp.department = d.department_id
                JOIN position p ON jp.position_code = p.position_id
                WHERE jp.job_id = :job_id
            ", ['job_id' => $job->job_id]);
        } else {
            $positions = DB::select("
                SELECT 
                    jp.position_code,
                    jp.job_position_id,
                    jp.level,
                    jp.qualification,
                    jp.salary,
                    jp.experience,
                    jp.skills,
                    jp.status,
                    d.department_name,
                    p.position_name,
                    (
                        SELECT COUNT(*) 
                        FROM applications a 
                        WHERE a.job_id = jp.job_id 
                        AND a.job_position_id = jp.job_position_id
                    ) as application_count
                FROM job_positions jp
                JOIN department d ON jp.department = d.department_id
                JOIN position p ON jp.position_code = p.position_id
                WHERE jp.job_id = :job_id
                AND p.position_name LIKE :like_position1
                ORDER BY 
                    CASE 
                        WHEN p.position_name = :exact_position THEN 1
                        WHEN p.position_name LIKE :start_position THEN 2
                        WHEN p.position_name LIKE :like_position2 THEN 3
                        ELSE 4
                    END
            ", [
                'job_id' => $job->job_id,
                'exact_position' => $job_positions_input,
                'start_position' => $job_positions_input . '%',
                'like_position1' => '%' . $job_positions_input . '%',
                'like_position2' => '%' . $job_positions_input . '%'
            ]);

            foreach ($positions as $pos) {
                if ($pos->position_name === $job_positions_input) {
                    $match_score += 100;
                } elseif (str_starts_with($pos->position_name, $job_positions_input)) {
                    $match_score += 90;
                } elseif (stripos($pos->position_name, $job_positions_input) !== false) {
                    $match_score += 70;
                }
            }
        }

        $job->job_positions = $positions;
        $job->match_score = $match_score;
    }

    // เรียงลำดับตาม match_score สูงสุด
    usort($jobs, function ($a, $b) {
        return $b->match_score <=> $a->match_score;
    });

    return $jobs;

    }

public function list_of_job_applicants($request)
{
    $job_id = $request->input('job_id');
    $job_position_id = $request->input('job_position_id');

    $applicants = DB::table('applications as app')
        ->join('job_positions as jobp', 'app.job_position_id', '=', 'jobp.job_position_id')
        ->where('app.job_id', $job_id)
        ->where('app.job_position_id', $job_position_id)
        ->select(
            'app.application_id',
            'app.job_id',
            'app.job_position_id',
            'app.photo_file',
            'app.prefix_th',
            'app.applicant_name',
            'app.phone',
            'jobp.position_name',
            'app.result',
            'app.status',
            'app.date_time'
        )
        ->get();


        return $applicants;
    }
public function view_of_job_applicants($id)
{
    $applicant_id = $id;

    // ข้อมูลหลักของผู้สมัคร
    $applicant = DB::table('applications as app')
        ->join('job_positions as jobp', 'app.job_position_id', '=', 'jobp.job_position_id')
        ->where('app.application_id', $applicant_id)
        ->select(
            'app.*',
            'jobp.position_name'
        )
        ->first();

    if (!$applicant) {
        return response()->json([
            'status' => false,
            'message' => 'ไม่พบข้อมูลผู้สมัคร'
        ], 404);
    }

    // ตารางย่อยทั้งหมด
    $related_tables = [
        'work_experience',
        'family_info',
        'typing_skills',
        'training',
        'health_conditions',
        'foreign_language_skills',
        'education_history',
        'driving_skills',
        'document_uploads'
    ];

    $related_data = [];

    foreach ($related_tables as $table) {
        $related_data[$table] = DB::table($table)
            ->where('application_id', $applicant_id)
            ->get();
    }

    // รวมทั้งหมดใน array
    return response()->json([
        'status' => true,
        'applicant' => $applicant,
        'details' => $related_data
    ]);
}
public function find_an_application($id)
{
    $id_card = $id;

    // ดึงผู้สมัครทั้งหมดที่มีเลขบัตรประชาชนนี้
    $applicants = DB::table('applications as app')
        ->join('job_positions as jobp', 'app.job_position_id', '=', 'jobp.job_position_id')
        ->where('app.id_card', $id_card)
        ->select('app.*', 'jobp.position_name')
        ->get(); // ใช้ get() เพราะอาจมีหลายคน

    if ($applicants->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'ไม่พบข้อมูลผู้สมัคร'
        ], 404);
    }

    // ตารางย่อยทั้งหมด
    $related_tables = [
        'work_experience',
        'typing_skills',
        'training',
        'health_conditions',
        'foreign_language_skills',
        'education_history',
        'driving_skills',
        'document_uploads'
    ];

    $result = [];

    // วนลูปผู้สมัครแต่ละคน
    foreach ($applicants as $applicant) {
        $related_data = [];

        // ดึงข้อมูลจากทุกตารางย่อยตาม application_id
        foreach ($related_tables as $table) {
            $related_data[$table] = DB::table($table)
                ->where('application_id', $applicant->application_id)
                ->get();
        }

        // เพิ่มเข้า array หลัก
        $result[] = [
            'applicant' => $applicant,
            'details' => $related_data
        ];
    }

    // ส่งผลลัพธ์กลับ
    return response()->json([
        'status' => true,
        'data' => $result
    ]);
}


/// อัพเดทสถานะใบสมัครงาน
    public function status_applications($request)
    {
        $application_id = $request->input('application_id'); // ต้องระบุผู้สมัคร
        $status = $request->input('status');

        if (!$application_id || !is_numeric($application_id)) {
            return false;
        } 

        // ถ้าสถานะเป็น "ผ่านทั้งหมด"
        if ($status === 'ผ่านทั้งหมด') {
            $result = 'ผ่าน';
            DB::table('applications')
                ->where('application_id', $application_id)
                ->update([
                    'status' => $status,
                    'result' => $result
                ]);
        } else {
            // ถ้าไม่ใช่ "ผ่านทั้งหมด" ก็อัปเดตเฉพาะสถานะ
            DB::table('applications')
                ->where('application_id', $application_id)
                ->update([
                    'status' => $status
                ]);
        }

        return true;
    }



}




 


























// $applicants = DB::select("
//         SELECT 
//             app.application_id,
//             app.job_id,
//             app.job_position_id,
//             app.photo_file,
//             app.prefix_th,
//             app.applicant_name,
//             app.phone,
//             jobp.position_name,
//             app.result,
//             app.status,
//             app.date_time
//         FROM applications app
//         JOIN job_positions jobp ON app.job_position_id = jobp.job_position_id
//         WHERE app.job_id = :job_id AND app.job_position_id = :job_position_id
//     ", [
//         'job_id' => $job_id,
//         'job_position_id' => $job_position_id
//     ]);