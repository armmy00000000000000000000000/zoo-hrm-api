<?php

// app/Models/sql/Get.php
namespace App\Models\Resource\permission;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class model_permission extends Model
{
    public function list_permissions()
    {
  

        $menus = DB::select("SELECT * FROM menus");

        if (empty($menus)) return [];

        return $menus; 
    }
    public function view_permission($id): mixed
    {
  

        $menus = DB::selectOne("SELECT * FROM menus WHERE id = :id", [
            'id' => $id
        ]);

        if (empty($menus)) return [];

      
            $user_menu_permissions = DB::select("SELECT * FROM `sub_menus` WHERE `menu_id`   = :menu_id ", [
                'menu_id' => $menus->id
            ]);
            $menus->user_menu_permissions = $user_menu_permissions;
   
        return $menus; 
    }

    public function add_permission($request)
    {
        $name = $request->input('menu_name');
        $details = $request->input('details');
        $user_menu_permissions = $request->input('sub_menu_permissions');

        if (empty($name) || empty($details) || empty($user_menu_permissions)) {
            return false; // ข้อมูลไม่ครบ
        }

        $checkDuplicate = DB::selectOne("SELECT * FROM menus WHERE menu_name = :menu_name", [
            'menu_name' => $name
        ]);
        if ($checkDuplicate) {
            return 'duplicate'; // ชื่อบทบาทนี้ถูกใช้ไปแล้ว
        }

        // บันทึกข้อมูลบทบาทใหม่
        $result = DB::insert(
            "INSERT INTO `menus` (`menu_name`, `details`) VALUES (?, ?)",
            [
                $name,
                $details
            ]
        );

        // ถ้า insert สำเร็จ ดึง id ล่าสุด
        if ($result) {
            $lastId = DB::getPdo()->lastInsertId(); // ดึง ID ที่เพิ่งเพิ่มใหม่จากตาราง roles

            foreach ($user_menu_permissions as $permission) {
                // ตรวจสอบว่ามีค่า menu_id หรือไม่
                if (empty($permission['sub_menu_name'])) {
                    continue;
                }
                $sub_menu_name = $permission['sub_menu_name'];
                $details = $permission['details'];
                // ตรวจสอบว่ามีสิทธิ์นี้อยู่แล้วหรือไม่
                $exists = DB::selectOne(
                    "SELECT 1 FROM sub_menus WHERE sub_menu_name = :sub_menu_name",
                    [
                        'sub_menu_name' => $sub_menu_name
                    ]
                );
                if ($exists) {
                    continue; // ถ้ามีอยู่แล้ว ให้ข้ามไป
                }
                // เพิ่มสิทธิ์เฉพาะที่ยังไม่มี
                DB::insert(
                    "INSERT INTO sub_menus (menu_id,sub_menu_name, details) VALUES (?, ?, ?)",
                    [
                        $lastId,
                        $sub_menu_name,
                        $details
                    ]
                );
            }
        }
            return true; // ถ้า insert สำเร็จ
    }// ใน model
    public function edit_permissions($request, $id)
    {
        $details = $request->input('details');

        if (empty($details)) {
            return false; // ข้อมูลไม่ครบ
        }

        $update_roles = DB::update(
            "UPDATE `menus` SET `details` = ? WHERE `id` = ?",
            [
                $details,
                $id
            ]
        );

        if ($update_roles > 0) {
            return true; // ถ้า update สำเร็จ
        } else {
            return 'not_found'; // ไม่มีข้อมูลถูกอัปเดต (อาจจะไม่มี record นี้)
        }
    }

    public function delete_permission($id)
    {
        if ($id === null) {
            return false; // ไม่พบ Role ID
        }

        // ลบข้อมูลจากตาราง user_menu_permissions ที่เกี่ยวข้องกับ roles_id นี้
        DB::table('sub_menus')->where('menu_id', $id)->delete();

        // ลบข้อมูลจากตาราง roles
        $result = DB::table('menus')->where('id', $id)->delete();

        return $result > 0; // คืนค่าความสำเร็จของการลบ
    }


}
