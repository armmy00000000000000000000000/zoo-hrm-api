<?php

// app/Models/sql/Get.php
namespace App\Models\Resource\roles;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class model_roled extends Model
{
    public function list_roles()
    {
  

        $users = DB::select("SELECT * FROM roles");

        if (empty($users)) return [];

        return $users; 
    }
    public function view_roles($id): mixed
    {
  

        $roles = DB::selectOne("SELECT * FROM roles WHERE id_roles = :id", [
            'id' => $id
        ]);

        if (empty($roles)) return [];

      
            $user_menu_permissions = DB::select("SELECT menus.id,menus.menu_name,menus.details FROM `user_menu_permissions` JOIN menus ON user_menu_permissions.menu_id = menus.id WHERE `roles_id`   = :id_roles ", [
                'id_roles' => $roles->id_roles
            ]);
            $roles->user_menu_permissions = $user_menu_permissions;
   
        return $roles; 
    }

    public function add_roles($request)
    {
        $name = $request->input('name');
        $detail = $request->input('detail');
        $user_menu_permissions = $request->input('user_menu_permissions');

        if (empty($name) || empty($detail) || empty($user_menu_permissions)) {
            return false; // ข้อมูลไม่ครบ
        }

        $checkDuplicate = DB::selectOne("SELECT * FROM roles WHERE name = :name", [
            'name' => $name
        ]);
        if ($checkDuplicate) {
            return 'duplicate'; // ชื่อบทบาทนี้ถูกใช้ไปแล้ว
        }

        // บันทึกข้อมูลบทบาทใหม่
        $result = DB::insert(
            "INSERT INTO `roles` (`name`, `detall`) VALUES (?, ?)",
            [
                $name,
                $detail
            ]
        );

        // ถ้า insert สำเร็จ ดึง id ล่าสุด
        if ($result) {
            $lastId = DB::getPdo()->lastInsertId(); // ดึง ID ที่เพิ่งเพิ่มใหม่จากตาราง roles

            foreach ($user_menu_permissions as $permission) {
                // ตรวจสอบว่ามีค่า menu_id หรือไม่
                if (empty($permission['menu_id'])) {
                    continue;
                }
                $menuId = $permission['menu_id'];
                // ตรวจสอบว่ามีสิทธิ์นี้อยู่แล้วหรือไม่
                $exists = DB::selectOne(
                    "SELECT 1 FROM user_menu_permissions WHERE roles_id = :roles_id AND menu_id = :menu_id",
                    [
                        'roles_id' => $lastId,
                        'menu_id' => $menuId
                    ]
                );
                if ($exists) {
                    continue; // ถ้ามีอยู่แล้ว ให้ข้ามไป
                }
                // เพิ่มสิทธิ์เฉพาะที่ยังไม่มี
                DB::insert(
                    "INSERT INTO user_menu_permissions (roles_id, menu_id) VALUES (?, ?)",
                    [
                        $lastId,
                        $menuId
                    ]
                );
            }
        }
            return true; // ถ้า insert สำเร็จ
    }// ใน model
    public function edit_roles($request, $id)
    {
        $name = $request->input('name');
        $detail = $request->input('detail');

        if (empty($name) || empty($detail)) {
            return false; // ข้อมูลไม่ครบ
        }

        $update_roles = DB::update(
            "UPDATE `roles` SET `name` = ?, `detall` = ? WHERE `id_roles` = ?",
            [
                $name,
                $detail,
                $id
            ]
        );

        if ($update_roles > 0) {
            return true; // ถ้า update สำเร็จ
        } else {
            return 'not_found'; // ไม่มีข้อมูลถูกอัปเดต (อาจจะไม่มี record นี้)
        }
    }


    public function add_roles_permission($request)
    {

        $user_menu_permissions = $request->input('user_menu_permissions'); // รูปแบบ: [ {'menu_id': 1, 'roles_id': 2}, ... ]

        if (empty($user_menu_permissions)) {
            return false; // ข้อมูลไม่ครบ
        }

        foreach ($user_menu_permissions as $permission) {
            // ตรวจสอบว่ามี menu_id และ roles_id
            if (empty($permission['menu_id']) || empty($permission['roles_id'])) {
                continue;
            }
            $menuId = $permission['menu_id'];
            $rolesId = $permission['roles_id'];
            // ตรวจสอบว่ามีสิทธิ์นี้อยู่แล้วหรือยัง
            $exists = DB::table('user_menu_permissions')
                        ->where('roles_id', $rolesId)
                        ->where('menu_id', $menuId)
                        ->exists();

            if ($exists) {
                continue; // มีแล้ว ข้าม
            }
            // เพิ่มสิทธิ์ใหม่
            DB::table('user_menu_permissions')->insert([
                'roles_id' => $rolesId,
                'menu_id' => $menuId
            ]);
        }
        return true;
    }


    public function sync_roles_permission($request)
    {
        $rolesId = $request->input('roles_id');
        $menuIds = $request->input('menu_id'); // ตัวอย่าง: [1, 2, 5]

        if (empty($rolesId) || !is_array($menuIds)) {
            return false;
        }

        // ลบ menu_id ที่เลือกออก (เฉพาะรายการเหล่านี้)
        $deleted = DB::table('user_menu_permissions')
                    ->where('roles_id', $rolesId)
                    ->whereIn('menu_id', $menuIds)
                    ->delete();

        if ($deleted === 0) {
            return 'not_found'; // ไม่มีรายการใดถูกลบ
        }

        return true;
    }


    public function delete_roles($id)
    {
        if ($id === null) {
            return false; // ไม่พบ Role ID
        }

        // ลบข้อมูลจากตาราง user_menu_permissions ที่เกี่ยวข้องกับ roles_id นี้
        DB::table('user_menu_permissions')->where('roles_id', $id)->delete();

        // ลบข้อมูลจากตาราง roles
        $result = DB::table('roles')->where('id_roles', $id)->delete();

        return $result > 0; // คืนค่าความสำเร็จของการลบ
    }


}
