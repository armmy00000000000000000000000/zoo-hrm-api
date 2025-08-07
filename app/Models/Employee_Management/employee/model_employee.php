<?php

namespace App\Models\Employee_Management\employee;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class model_employee extends Model
{

// public function list_employee($request)
// {
//     $agency_id = $request->input('agency_id');
//     $job_position_id = $request->input('job_position_id');

//     $applicants = DB::table('applications as app')
//         ->join('employees as em', 'app.application_id', '=', 'em.application_id')
//         ->join('position as poti', 'em.position', '=', 'poti.position_id')
//         ->join('agency as ag', 'em.work_location', '=', 'ag.id')
//         ->where('em.position', $job_position_id)
//         ->where('em.work_location', $agency_id)
//         ->select(
//             'em.*',
//             'app.*',
//             'ag.name as agency_name',
//             'poti.position_name'
//         )
//         ->get();

//     return $applicants;
// }


public function list_employee($request)
{
    $agency_id = $request->input('agency_id');
    $job_position_id = $request->input('job_position_id');

    $applicants = DB::table('applications as app')
        ->join('employees as em', 'app.application_id', '=', 'em.application_id')
        ->join('position as poti', 'em.position', '=', 'poti.position_id')
        ->join('agency as ag', 'em.work_location', '=', 'ag.id')
        ->where(function ($query) use ($agency_id, $job_position_id) {
            if (!empty($job_position_id)) {
                $query->orWhere('em.position', $job_position_id);
            } 
            if (!empty($agency_id)) {
                $query->orWhere('em.work_location', $agency_id);
            }
        })
        ->select(
            'em.*',
            'app.*',
            'ag.name as agency_name',
            'poti.position_name'
        )
        ->get();

    return $applicants;
}


}