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


}