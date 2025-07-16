<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Response\Response;  // import Response class ให้ถูกต้อง
use App\Models\sql\user\Delete;
use App\Models\sql\user\Get;
use App\Models\sql\user\Post;
use App\Models\sql\user\Put;

class UserController extends Controller
{
    protected $response;

    public function __construct()
    {
        $this->response = new Response(); 
    }

    public function getAll($id = null)
    {
        if ($id === null) {
            return $this->response->error('เกิดข้อผิดพลาด: ไม่พบ Department ID', 400);
        }
        $model = new Get();
        $users = $model->Getemproiye($id);
        if (empty($users)) {
            return $this->response->error('ไม่พบข้อมูลสำหรับ Department ID ' . $id, 404);
        }
        return $this->response->success($users, 'ดึงข้อมูลพนักงานสำเร็จ');
    }

       public function getID($id)
    {
        if ($id === null) {
            $this->response->error('เกิดข้อผิดพลาด: ไม่พบ Ures ID', 400);
        }
        $model = new Get();
        $users = $model->GetuserID($id);

        if (empty($users)) {
            return $this->response->error('ไม่พบข้อมูลสำหรับ User ID ' . $id, 404);
        }
       
   
        $this->response->success($users, 'Data fetched successfully');
    }

    public function addData(Request $request)
    {
        $model = new Post();
        $result = $model->Register($request);

        if ($result === true) {
            return $this->response->success([], 'สมัครสมาชิกสำเร็จ', 201);
        } elseif ($result === 'duplicate') {
            return $this->response->error('ชื่อผู้ใช้นี้ถูกใช้ไปแล้ว', 409);
        } else {
            return $this->response->error('เกิดข้อผิดพลาดในการบันทึกข้อมูล', 500);
        }
}

    public function updateUser(Request $request)
    {
       $id = $request->input('id');
        if ($id === null) {
            return $this->response->error('เกิดข้อผิดพลาด: ไม่พบ User ID', 400);
        }
        $model = new Put();
        $result = $model->Updatedata($request);

        if ($result === true) {
            return $this->response->success([], 'อัพเดทข้อมูลสำเร็จ', 200);
        } elseif ($result === 'duplicate') {
            return $this->response->error('ไม่พบข้อมูลสำหรับ User ID ' . $id, 404);
        } else {
            return $this->response->error('เกิดข้อผิดพลาดในการอัพเดทข้อมูล', 500);
        }
    }

    public function deleteUser($id)
    {
        if ($id === null) {
            return $this->response->error('เกิดข้อผิดพลาด: ไม่พบ User ID', 400);
        }
        $model = new Delete();
        $result = $model->DeleteData($id);

        if ($result === true) {
            return $this->response->success([], 'ลบข้อมูลสำเร็จ', 200);
        } elseif ($result === false) {
            return $this->response->error('ไม่พบข้อมูลสำหรับ User ID ' . $id, 404);
        } else {
            return $this->response->error('เกิดข้อผิดพลาดในการลบข้อมูล', 500);
        }
    }

   protected function responseSuccess($res)
    {
        return response()->json(["status" => "success", "data" => $res], 200)
            ->header("Access-Control-Allow-Origin", "*")
            ->header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS");
    }

 
}


 
