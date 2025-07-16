<?php

namespace App\Http\Controllers\Resource;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Response\Response;  // import Response class ให้ถูกต้อง
use App\Models\Resource\agency\model_agency;
use App\Models\Resource\user\model_user;
use App\Models\Resource\roles\model_roled;
use App\Models\Resource\permission\model_permission;


class Controller_Resource extends Controller
{
    protected $response;

    public function __construct()
    {
        $this->response = new Response(); 
    }


///    /**     * ดึงข้อมูลagency    
        public function getAll_agency()
        {
            $agencies = model_agency::all();  // ใช้ Eloquent ดึงข้อมูลทั้งหมด

            if ($agencies->isEmpty()) {
                return $this->response->error('ไม่พบข้อมูลสำหรับ agency', 404);
            }

            return $this->response->success($agencies, 'ดึงข้อมูลหน่วยงานสำเร็จ');
        }

        public function Addagency(Request $request)
        {
            $agency = model_agency::create([
                'name' => $request->input('name'),
                'detail' => $request->input('detail')
            ]);

            return $this->response->success($agency, 'เพิ่มหน่วยงานสำเร็จ');
        }

//////// end of recent edits ///


///  /**   *ส่วนของข้อมูล user
        public function Adduser(Request $request)
        {
         $user = new model_user();
         $adduser = $user->Add_user($request);
           if($adduser === true){
               return $this->response->success([], 'เพิ่มผู้ใช้งานสำเร็จ', 201);
           } elseif ($adduser === 'duplicate') {
               return $this->response->error('ชื่อผู้ใช้นี้ถูกใช้ไปแล้ว', 409);
           } else {
               return $this->response->error('เกิดข้อผิดพลาดในการบันทึกข้อมูล', 500);

           }
        }
        public function getAll_user()
        {
            $agencies = new model_user();  // ใช้ Eloquent ดึงข้อมูลทั้งหมด
            $users = $agencies->list_user();  // เรียกใช้ method list_user จาก model_user
            if (empty($users)) {
                return $this->response->error('ไม่พบข้อมูลสำหรับ user', 404);
            }else {
                return $this->response->success($users, 'ดึงข้อมูลผู้ใช้งานระบบสำเร็จ');
            }

   
        }
        public function getID_user($id)
        {
            if ($id === null) {
                return $this->response->error('เกิดข้อผิดพลาด: ไม่พบ User ID', 400);
            }
            $agencies = new model_user();  // ใช้ Eloquent ดึงข้อมูลทั้งหมด
            $user = $agencies->view_user($id);  // เรียกใช้ method view_user จาก model_user

            if (empty($user)) {
                return $this->response->error('ไม่พบข้อมูลสำหรับ User ID ' . $id, 404);
            }else {
                return $this->response->success($user, 'ดึงข้อมูลผู้ใช้งานระบบสำเร็จ');
            }
        }
        public function Updateuser(Request $request,$id)
        {
            $model = new model_user();
            $result = $model->Update_user($request,$id);

            if ($result === true) {
                return $this->response->success([], 'อัพเดทข้อมูลผู้ใช้งานสำเร็จ', 200);
            } elseif ($result === 'not_found') {
                return $this->response->error('ไม่พบข้อมูลสำหรับ User ID', 404);
            } else {
                return $this->response->error('เกิดข้อผิดพลาดในการอัพเดทข้อมูล', 500);
            }
        }

        public function update_password(Request $request,$id)
        {
            if ($id === null) {
                return $this->response->error('เกิดข้อผิดพลาด: ไม่พบ User ID', 400);
            }
            $model = new model_user();
            $result = $model->Update_password($id,$request);

            if ($result === true) {
                return $this->response->success([], 'อัพเดทรหัสผ่านสำเร็จ', 200);
            } elseif ($result === 'not_found') {
                return $this->response->error('ไม่พบข้อมูลสำหรับ User ID', 404);
            } else {
                return $this->response->error('เกิดข้อผิดพลาดในการอัพเดทรหัสผ่าน', 500);
            }
        }
    
//////// end of recent edits ///

///////  *ส่วนชอง Roles
      public function List_roles()
        {
            $model = new model_roled();
            $roles = $model->list_roles();

            if (empty($roles)) {
                return $this->response->error('ไม่พบข้อมูลสำหรับ roles', 404);
            } else {
                return $this->response->success($roles, 'ดึงข้อมูล roles สำเร็จ');
            }
        }
      public function view_roles($id)
        {
            if ($id === null) {
                return $this->response->error('เกิดข้อผิดพลาด: ไม่พบ Role ID', 400);
            }
            $model = new model_roled();
            $roles = $model->view_roles($id);

            if (empty($roles)) {
                return $this->response->error('ไม่พบข้อมูลสำหรับ Role ID ' . $id, 404);
            } else {
                return $this->response->success($roles, 'ดึงข้อมูล roles สำเร็จ');
            }
        }
        public function Add_roles(Request $request)
            {
                $model = new model_roled();
                $result = $model->add_roles($request);
    
                if ($result === true) {
                    return $this->response->success([], 'เพิ่ม roles สำเร็จ', 201);
                } elseif ($result === 'duplicate') {
                    return $this->response->error('ชื่อ roles นี้ถูกใช้ไปแล้ว', 409);
                } else {
                    return $this->response->error('เกิดข้อผิดพลาดในการบันทึกข้อมูล', 500);
                }
            }
        public function edit_roles(Request $request,$id)
            {
                $model = new model_roled();
                $result = $model->edit_roles($request, $id);
                if ($result === true) {
                    return $this->response->success([], 'อัพเดท roles สำเร็จ', 200);
                } elseif ($result === 'not_found') {
                    return $this->response->error('ไม่พบข้อมูลสำหรับ Role ID'.$id, 404);
                } else {
                    return $this->response->error('เกิดข้อผิดพลาดในการอัพเดทข้อมูล', 500);
                }
            }


        public function add_roles_permission(Request $request)
        {
            $model = new model_roled();
            $result = $model->add_roles_permission($request);
            if ($result === true) {
                return $this->response->success([], 'เพิ่มสิทธิ์ roles สำเร็จ', 201);
            } elseif ($result === 'not_found') {
                return $this->response->error('ไม่พบข้อมูลสำหรับ Role ID', 404);
            } else {
                return $this->response->error('เกิดข้อผิดพลาดในการเพิ่มสิทธิ์', 500);
            }
        }
        
        public function delete_roles_permission(Request $request)
        {
      
            $model = new model_roled();
            $result = $model->sync_roles_permission($request);

            if ($result === true) {
                return $this->response->success([], 'ลบ roles สำเร็จ', 200);
            } elseif ($result === 'not_found') {
                return $this->response->error('ไม่พบข้อมูลสำหรับ Role ID', 404);
            } else {
                return $this->response->error('เกิดข้อผิดพลาดในการลบข้อมูล', 500);
            }
        }

        public function delete_roles($id)
        {
            if ($id === null) {
                return $this->response->error('เกิดข้อผิดพลาด: ไม่พบ Role ID', 400);
            }
            $model = new model_roled();
            $result = $model->delete_roles($id);

            if ($result === true) {
                return $this->response->success([], 'ลบ roles สำเร็จ', 200);
            } elseif ($result === 'not_found') {
                return $this->response->error('ไม่พบข้อมูลสำหรับ Role ID'.$id, 404);
            } else {
                return $this->response->error('เกิดข้อผิดพลาดในการลบข้อมูล', 500);
            }
        }

//////// end of recent edits ///

///////  *ส่วนชอง Premisson

        public function list_permission()
        {
            $model = new model_permission();
            $permissions = $model->list_permissions();

            if (empty($permissions)) {
                return $this->response->error('ไม่พบข้อมูลสำหรับ permissions', 404);
            } else {
                return $this->response->success($permissions, 'ดึงข้อมูล permissions สำเร็จ');
            }
        }

        public function view_permission($id)
        {
            if ($id === null) {
                return $this->response->error('เกิดข้อผิดพลาด: ไม่พบ Permission ID', 400);
            }
            $model = new model_permission();
            $permission = $model->view_permission($id);

            if (empty($permission)) {
                return $this->response->error('ไม่พบข้อมูลสำหรับ Permission ID ' . $id, 404);
            } else {
                return $this->response->success($permission, 'ดึงข้อมูล permissions สำเร็จ');
            }
        }

        public function add_permission(Request $request)
        {
            $model = new model_permission();
            $result = $model->add_permission($request);

            if ($result === true) {
                return $this->response->success([], 'เพิ่ม permissions สำเร็จ', 201);
            } elseif ($result === 'duplicate') {
                return $this->response->error('ชื่อ permissions นี้ถูกใช้ไปแล้ว', 409);
            } else {
                return $this->response->error('เกิดข้อผิดพลาดในการบันทึกข้อมูล', 500);
            }
        }

        public function edit_permission(Request $request, $id)
        {
            $model = new model_permission();
            $result = $model->edit_permissions($request, $id);

            if ($result === true) {
                return $this->response->success([], 'อัพเดท permissions สำเร็จ', 200);
            } elseif ($result === 'not_found') {
                return $this->response->error('ไม่พบข้อมูลสำหรับ Permission ID'.$id, 404);
            } else {
                return $this->response->error('เกิดข้อผิดพลาดในการอัพเดทข้อมูล', 500);
            }
        }

        public function delete_permission($id)
        {
            if ($id === null) {
                return $this->response->error('เกิดข้อผิดพลาด: ไม่พบ Permission ID', 400);
            }
            $model = new model_permission();
            $result = $model->delete_permission($id);
            if ($result === true) {
                return $this->response->success([], 'ลบ permissions สำเร็จ', 200);
            } elseif ($result === 'not_found') {
                return $this->response->error('ไม่พบข้อมูลสำหรับ Permission ID'.$id, 404);
            } else {
                return $this->response->error('เกิดข้อผิดพลาดในการลบข้อมูล', 500);
            }
        }

//////// end of recent edits ///


    }



 

