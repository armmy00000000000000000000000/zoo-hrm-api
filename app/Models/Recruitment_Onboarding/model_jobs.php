<?php

namespace App\Models\Recruitment_Onboarding\jobs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class model_jobs extends Model
{
    public function search_jobs($request)
    {
        $job_title = trim($request->input('job_title', ''));
        $agency_id = $request->input('agency_id', null);
        $job_positions_input = trim($request->input('job_positions', ''));

        // กรณีพิเศษ ถ้า job_title = "มา" ดึงทั้งหมด
        if ($job_title === 'มา') {
            $query = "SELECT job.*, agency.name 
                      FROM job 
                      JOIN agency ON job.agency_id = agency.id";
            $params = [];
        } else {
            $query = "SELECT job.*, agency.name 
                      FROM job 
                      JOIN agency ON job.agency_id = agency.id 
                      WHERE 1=1";
            $params = [];

            if ($job_title !== '') {
                $query .= " AND job.job_title LIKE :job_title";
                $params['job_title'] = '%' . $job_title . '%';
            }

            if (!empty($agency_id)) {
                $query .= " AND job.agency_id = :agency_id";
                $params['agency_id'] = $agency_id;
            }
        }

        $jobs = DB::select($query, $params);

        if (empty($jobs)) return [];

        foreach ($jobs as &$job) {
            $job->agency_name = $job->name;
            unset($job->name);

            if ($job_positions_input === '') {
                // กรณีไม่กรอง position_name
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
                        p.position_name
                    FROM job_positions jp
                    JOIN department d ON jp.department = d.department_id
                    JOIN position p ON jp.position_code = p.position_id
                    WHERE jp.job_id = :job_id
                ", ['job_id' => $job->job_id]);
            } else {
                // กรณีกรอง position_name
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
                        p.position_name
                    FROM job_positions jp
                    JOIN department d ON jp.department = d.department_id
                    JOIN position p ON jp.position_code = p.position_id
                    WHERE jp.job_id = :job_id
                    AND p.position_name LIKE :position_name
                ", [
                    'job_id' => $job->job_id,
                    'position_name' => '%' . $job_positions_input . '%'
                ]);
            }

            $job->job_positions = $positions;
        }

        return $jobs;
    }
    public function view_jobs($id)
        {

            $job_positions_input = $id;


                $query = "SELECT job.*, agency.name 
                        FROM job 
                        JOIN agency ON job.agency_id = agency.id WHERE job.job_id  = :job_id";
                        
            $params['job_id'] = $job_positions_input;

            $jobs = DB::select($query, $params);

            if (empty($jobs)) return [];

            foreach ($jobs as &$job) {
                $job->agency_name = $job->name;
                unset($job->name);

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
                            p.position_name
                        FROM job_positions jp
                        JOIN department d ON jp.department = d.department_id
                        JOIN position p ON jp.position_code = p.position_id
                        WHERE jp.job_id = :job_id
                    ", ['job_id' => $job->job_id]);
               

                $job->job_positions = $positions;
            }

            return $jobs;
        }


    public function list_position()
            {
            
                $positions = DB::select("SELECT * FROM position");

                if (empty($positions)) return [];

                return $positions; 
                
            }
     public function create_job($request)
        {
            try {
                // 1. รับค่าจาก $request
                $job_title       = $request->input('job_title');
                $job_date        = $request->input('job_date');
                $status          = $request->input('status');
                $total_positions = $request->input('total_positions');
                $agency_id       = $request->input('agency_id');

                // 2. หา job_number ล่าสุด
                // สมมติ job_number เป็น string 3 หลัก เช่น '001', '002', ...
                $lastJobNumber = DB::table('job')
                    ->select('job_number')
                    ->orderBy('job_number', 'desc')
                    ->limit(1)
                    ->value('job_number');

                if ($lastJobNumber) {
                    // แปลงเป็นเลขแล้ว +1
                    $newNumber = (int)$lastJobNumber + 1;
                } else {
                    // ถ้ายังไม่มีข้อมูลเลย เริ่มที่ 1
                    $newNumber = 1;
                }

                // แปลงเลขให้เป็นรูปแบบ 3 หลัก เช่น 1 -> 001
                // ปีปัจจุบันแบบ พ.ศ.
                $yearThai = date('Y') + 543;  // ปี ค.ศ. + 543 = ปี พ.ศ.

                // สร้างเลข 3 หลัก เช่น 001, 002
                $numberPart = str_pad($newNumber, 3, '0', STR_PAD_LEFT);

                // รวมเลขกับปี เช่น "001/2568"
                $job_number = $numberPart . '/' . $yearThai;


                // 3. INSERT into `job`
                DB::insert(
                    "INSERT INTO job (job_title, job_date, job_number, status, total_positions, agency_id)
                    VALUES (?, ?, ?, ?, ?, ?)",
                    [$job_title, $job_date, $job_number, $status, $total_positions, $agency_id]
                );

                // 4. ดึง job_id ล่าสุด
                $job_id = DB::getPdo()->lastInsertId();

                // 5. INSERT job_positions
                $positions = $request->input('job_positions');
                if (!is_array($positions)) {
                    return response()->json(['success' => false, 'error' => 'job_positions ต้องเป็น array'], 400);
                }

                foreach ($positions as $position) {
                    DB::insert(
                        "INSERT INTO job_positions (job_id, position_code, position_name, department, division, level, qualification, salary, experience, skills, status)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                        [
                            $job_id,
                            $position['position_code'],
                            $position['position_name'],
                            $position['department'],
                            $position['division'],
                            $position['level'],
                            $position['qualification'],
                            $position['salary'],
                            $position['experience'],
                            $position['skills'],
                            $position['status']
                        ]
                    );
                }

                return response()->json(['success' => true, 'message' => 'เพิ่มข้อมูลสำเร็จ'], 200);

            } catch (\Exception $e) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
        }



 public function upload_jobfile($request)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['message' => 'ไม่พบไฟล์ที่อัปโหลด'], 400);
        }

        $file = $request->file('file');

        // ตรวจสอบประเภทไฟล์ที่อนุญาต
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, $allowed)) {
            return response()->json(['message' => 'ไม่อนุญาตให้อัปโหลดไฟล์ประเภทนี้'], 422);
        }

        // ตั้งชื่อไฟล์ใหม่เพื่อกันซ้ำ
        $filename = time() . '_' . uniqid() . '.' . $extension;

        // ย้ายไฟล์ไปไว้ใน public/uploads
        $uploadPath = base_path('public/jobfile');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $file->move($uploadPath, $filename);

        // ส่ง URL กลับ
        $url = url('jobfile/' . $filename);

        $update_job = DB::update(
            "UPDATE job SET job_file = ? WHERE job_id = ?",
            [$url, $request->input('job_id')]
        );
        if ($update_job === false) {
            return response()->json(['message' => 'ไม่สามารถอัปเดตข้อมูลไฟล์ได้'], 500);
        }


        return response()->json([
            'message' => 'อัปโหลดไฟล์สำเร็จ',
            'filename' => $filename,
            'url' => $url
        ]);
    }

    public function create_position($request)
    {
        // รับค่าจาก request
        $position_name = $request->input('position_name');


        // ตรวจสอบว่าชื่อ position มีอยู่แล้วหรือไม่
        $existingPosition = DB::table('position')
            ->where('position_name', $position_name)
            ->first();

        if ($existingPosition) {
            return response()->json(['message' => 'ตำแหน่งงานนี้มีอยู่แล้ว'], 409);
        }

        // สร้างตำแหน่งงานใหม่
        DB::table('position')->insert([
            'position_name' => $position_name
        ]);

        return response()->json(['message' => 'สร้างตำแหน่งงานสำเร็จ'], 201);
    }

    public function update_status_jobs($request,$id)
    {
        $job_positions = DB::table('job_positions')->where('job_position_id', $id)->first();

        if (!$job_positions) {
            return false;
        }

        $job_positions_update = DB::table('job_positions')
            ->where('job_position_id', $id)
            ->update(['status' => $request->input('status')]);

        if ($job_positions_update === false) {
            return false;
        }
        return true;

}
}
