<?php

namespace App\Models\Employee_Management\personal_info;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class model_personal_info extends Model
{


public function view_personal_info($id)
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
        'employees',
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
        if ($table === 'employees') {
            // JOIN ตาราง agency และ position เฉพาะ employees
            $related_data[$table] = DB::table('employees as emp')
                ->join('agency as ag', 'emp.work_location', '=', 'ag.id')
                ->join('position as po', 'emp.position', '=', 'po.position_id')
                ->where('emp.application_id', $applicant_id)
                ->select('emp.*', 'ag.name', 'po.position_name')
                ->get();
        } else {
            // ตารางอื่นๆ ดึงตรงๆ
            $related_data[$table] = DB::table($table)
                ->where('application_id', $applicant_id)
                ->get();
        }
    }

    // รวมทั้งหมดใน array
    return response()->json([
        'status' => true,
        'applicant' => $applicant,
        'details' => $related_data
    ]);
}
 



public function update_personal_info($data)
{
    $applicant_id = $data['application_id'];
  
    $update_employees = DB::table('employees')
        ->where('application_id', $applicant_id)
        ->update([
            'gender' => $data['gender'],
            'phone' => $data['phone'],
            'position' => $data['position'],
            'employee_type' => $data['employee_type'], 
            'start_date' => $data['start_date'],
            'salary' => $data['salary'],
            'pay_type' => $data['pay_type'],
            'work_location' => $data['work_location'],
            'bank_account' => $data['bank_account'],
            'bank_account_number' => $data['bank_account_number'],
            'bank_account_name' => $data['bank_account_name'],
            'prefix' => $data['prefix_th'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'nationality' => $data['nationality']
        ]);

    $update_applications = DB::table('applications')
        ->where('application_id', $applicant_id)
        ->update([
            'prefix_th' => $data['prefix_th'],
            'applicant_name' => $data['first_name'] . ' ' . $data['last_name'],
            'prefix_en' => $data['prefix_en'], 
            'first_name_en' => $data['first_name_en'],
            'last_name_en' => $data['last_name_en'],
            'birth_date' => $data['birth_date'],
            'nationality' => $data['nationality'],
            'ethnicity' => $data['ethnicity'],
            'religion' => $data['religion'],
            'height_cm' => $data['height_cm'],
            'weight_kg' => $data['weight_kg'],
            'marital_status' => $data['marital_status'],
            'military_status' => $data['military_status'],
            'has_been_jailed' => $data['has_been_jailed'],
            'ever_fired' => $data['ever_fired']
        ]);

    if ($update_employees || $update_applications) {
        return true;
    } else {
        return false;
    }

}
}