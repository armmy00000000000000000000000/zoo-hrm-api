<?php

namespace App\Models\Recruitment_Onboarding\interview_evaluation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\uploadDocumentFile;
class model_interview_evaluation extends Model
{




public function list_of_eligible_candidates($request){
    $job_title = trim($request->input('job_title', ''));
    $agency_id = $request->input('agency_id', null);
    $job_positions_input = trim($request->input('job_positions', ''));
    $search_type = $request->input('search_type', 'and'); // 'and' หรือ 'or'

    $params = [];
    $query = "SELECT job.*, agency.name 
            FROM job 
            JOIN agency ON job.agency_id = agency.id 
            WHERE 1=1"; 
    // แสดงเฉพาะปีปัจจุบัน
  $query .= " AND YEAR(job.job_date) = :current_year";
  $params['current_year'] = date('Y'); // เช่น 2025

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

        foreach ($positions as &$pos) {
        $candidates = DB::table('applications as app')
            ->join('job_positions as jobp', 'app.job_position_id', '=', 'jobp.job_position_id')
            ->where('app.job_id', $job->job_id)
            ->where('app.job_position_id', $pos->job_position_id)
            ->whereIn('app.status', ['ผ่านรอบแรก', 'ไม่ผ่าน']) // <-- เงื่อนไขที่ถูกต้อง
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


       $pos->list_of_eligible_candidates = $candidates;
    }
   // เพิ่มข้อมูลห้องสอบ (room)
        $room = DB::table('exam_announcements')
            ->where('job_id', $job->job_id)
            ->first();

        $job->room = $room;
        $job->job_positions = $positions;
        $job->match_score = $match_score;
    }

    // เรียงลำดับตาม match_score สูงสุด
    usort($jobs, function ($a, $b) {
        return $b->match_score <=> $a->match_score;
    });

    return $jobs;

    }


public function create_room($request)
{
    $job_id = $request->input('job_id');
    if (!$job_id) {
        return response()->json(['error' => 'job_id is required'], 400);
    }

    $data = [];

    $fields = [
        'title',
        'title2',
        'announcement_date',
        'announcement_date2',
        'exam_location',
        'schedule',
        'details',
        'announce',
        'announce2'
    ];

    foreach ($fields as $field) {
        if ($request->filled($field)) {
            $data[$field] = $request->input($field);
        }
    }

    // จัดการไฟล์แนบ
    $uploader = new uploadDocumentFile();

    $uploadResult = $uploader->uploadDocumentFile($request, 'file');
    if ($uploadResult && !str_starts_with($uploadResult, 'HTTP')) {
        $data['document_path'] = $uploadResult;
    }

    $uploadResult2 = $uploader->uploadDocumentFile($request, 'file2');
    if ($uploadResult2 && !str_starts_with($uploadResult2, 'HTTP')) {
        $data['document_path2'] = $uploadResult2;
    }

    $existing = DB::table('exam_announcements')->where('job_id', $job_id)->first();

    if ($existing) {
        if (!empty($data)) {
            DB::table('exam_announcements')->where('job_id', $job_id)->update($data);
        }
    } else {
        $data['job_id'] = $job_id;
        DB::table('exam_announcements')->insert($data);
    }

    return true;
}


//// ประกาศหรายชื่อผู้มีสิทธิ์สอบ
public function list_of_eligible_candidates_users($request){
    $job_title = '';
    $agency_id = '';
    $job_positions_input = '';
    $search_type = $request->input('search_type', 'and'); // 'and' หรือ 'or'

    $params = [];

    $query = "SELECT job.job_number,exam_announcements.*, agency.name 
            FROM job 
            JOIN agency ON job.agency_id = agency.id join exam_announcements ON job.job_id = exam_announcements.job_id
            WHERE 1=1";

    // แสดงเฉพาะปีปัจจุบัน
    $query .= " AND YEAR(job.job_date) = :current_year AND exam_announcements.announce = '1'";
    $params['current_year'] = date('Y');



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

    $job->match_score = 0; // กำหนดเริ่มต้นใน object

    if ($job_title !== '') {
        if ($job->job_title === $job_title) {
            $job->match_score += 100;
        } elseif (str_starts_with($job->job_title, $job_title)) {
            $job->match_score += 90;
        } elseif (stripos($job->job_title, $job_title) !== false) {
            $job->match_score += 70;
        }
    }
}


    // เรียงลำดับตาม match_score สูงสุด
    usort($jobs, function ($a, $b) {
        return $b->match_score <=> $a->match_score;
    });

    return $jobs;

    }


/// รายละเอียดห้องสอบ
public function vidw_of_eligible_candidates_users($request){
    $job_title = '';
    $job_id = $request->input('job_id');
    $job_positions_input = '';
    $search_type = $request->input('search_type', 'and'); // 'and' หรือ 'or'

 $params = [];

    $query = "SELECT job.job_number,exam_announcements.*, agency.name 
            FROM job 
            JOIN agency ON job.agency_id = agency.id join exam_announcements ON job.job_id = exam_announcements.job_id
            WHERE 1=1";

    // แสดงเฉพาะปีปัจจุบัน
    $query .= " AND YEAR(job.job_date) = :current_year AND exam_announcements.announce = '1'";
    $params['current_year'] = date('Y');



    // เงื่อนไข job_title
    if ($job_title !== '') {
        $query .= " AND job.job_title LIKE :like_job_title";
        $params['like_job_title'] = '%' . $job_title . '%';
        $params['exact_job_title'] = $job_title;
        $params['start_job_title'] = $job_title . '%';
        $params['like_job_title2'] = '%' . $job_title . '%';
    }

    // เงื่อนไข agency
    if (!empty($job_id)) {
        $query .= " AND job.job_id = :job_id";
        $params['job_id'] = $job_id;
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

        foreach ($positions as &$pos) {
        $candidates = DB::table('applications as app')
            ->join('job_positions as jobp', 'app.job_position_id', '=', 'jobp.job_position_id')
            ->where('app.job_id', $job->job_id)
            ->where('app.job_position_id', $pos->job_position_id)
            ->whereIn('app.status', ['ผ่านรอบแรก']) // <-- เงื่อนไขที่ถูกต้อง
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


       $pos->list_of_eligible_candidates = $candidates;
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
/// กรอกคะแนนสอบล

  public function enter_your_score($request)
    {
        $written_score   = $request->input('written_score');
        $interview_score = $request->input('interview_score');
        $application_id  = $request->input('application_id');

        if (!$written_score || !$interview_score || !$application_id) {
            return false; // หรือ throw exception ตามที่ต้องการ
        }

        // คำนวณคะแนนรวม
        $final_score = $written_score + $interview_score;

        // อัพเดทลง database
        $updated = DB::table('applications')
            ->where('application_id', $application_id)
            ->update([
                'written_score'   => $written_score,
                'interview_score' => $interview_score,
                'final_score'     => $final_score
            ]);

        if ($updated) {
            return true;
        } else {
            return false;
        }
    }
}




 

























