<?php

namespace App\Models\Recruitment_Onboarding\new_employee_info;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\uploadDocumentFile;
class model_new_employee_info extends Model
{


//// ประกาศหรายชื่อผู้ถูกคัดเลือก
public function the_names_have_been_selected($request){
    $job_title = '';
    $agency_id = '';
    $job_positions_input = '';
    $search_type = $request->input('search_type', 'and'); // 'and' หรือ 'or'

    $params = [];

    $query = "SELECT job.job_number,exam_announcements.title2, agency.name,exam_announcements.document_path2,exam_announcements.announcement_date2
            FROM job 
            JOIN agency ON job.agency_id = agency.id join exam_announcements ON job.job_id = exam_announcements.job_id
            WHERE 1=1";

    // แสดงเฉพาะปีปัจจุบัน
    $query .= " AND YEAR(job.job_date) = :current_year AND exam_announcements.announce2 = '1'";
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

    }

    // เรียงลำดับตาม match_score สูงสุด
    usort($jobs, function ($a, $b) {
        return $b->match_score <=> $a->match_score;
    });

    return $jobs;

    }


/// รายละเอียดห้องสอบ
public function viewthe_names_have_been_selected($request){
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
            ->whereIn('app.status', ['ผ่านทั้งหมด']) // <-- เงื่อนไขที่ถูกต้อง
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
                'app.written_score',
                'app.interview_score',
                'app.final_score',
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
/// ลงทะเบียนพนักงานใหม่
public function register_new_employee($request)
{
    // ดึงเลขล่าสุดจาก employee_id ในตาราง employees
    $last = DB::table('employees')
        ->select(DB::raw('MAX(CAST(SUBSTRING(employee_id, 2) AS UNSIGNED)) AS last_number'))
        ->first();

    $nextNumber = ($last->last_number ?? 0) + 1;
    $employee_id = 'P' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

    // ดึงข้อมูลที่ส่งมา
    $data = $request->only([
        'application_id', 'prefix', 'first_name', 'last_name', 'id_card', 'birth_date',
        'nationality', 'gender', 'phone', 'email', 'address', 'position', 'department',
        'employee_type', 'start_date', 'work_location', 'salary', 'pay_type',
        'bank_account','bank_account_number', 'bank_account_name', 'user_account', 'user_email', 'work_start_date'
    ]);

    $data['employee_id'] = $employee_id;
    $data['status'] = 'active';


    // บันทึกข้อมูล
    DB::table('employees')->insert($data);

    return response()->json([
        'message' => 'สร้างรหัสพนักงานใหม่เรียบร้อย',
        'employee_id' => $employee_id
    ]);
}


public function getNewEmployee()
{
    // ใช้ DB::select แบบ raw SQL พร้อม parameter binding
    $newEmployee = DB::select("
        SELECT app.*
        FROM employees em
        RIGHT JOIN applications app ON em.application_id = app.application_id
        WHERE em.status IS NULL AND app.result = 'ผ่าน'
    ", []);

    if (empty($newEmployee)) {
        return response()->json(['message' => 'ไม่พบข้อมูลพนักงานใหม่'], 404);
    }

    // เนื่องจากผลลัพธ์ DB::select จะได้เป็น array เสมอ → ส่งเฉพาะแถวแรก
    return $newEmployee;
}
public function getNewEmployeeInfo($application_id)
{
    // ใช้ DB::select แบบ raw SQL พร้อม parameter binding
    $newEmployeeInfo = DB::selectOne("
        SELECT app.*
        FROM employees em
        RIGHT JOIN applications app ON em.application_id = app.application_id
        WHERE em.status IS NULL AND app.result = 'ผ่าน' AND app.application_id = ?
    ", [$application_id]);

    if (empty($newEmployeeInfo)) {
        return response()->json(['message' => 'ไม่พบข้อมูลพนักงานใหม่'], 404);
    }

    // เนื่องจากผลลัพธ์ DB::select จะได้เป็น array เสมอ → ส่งเฉพาะแถวแรก
    return $newEmployeeInfo;
}



}




 

























