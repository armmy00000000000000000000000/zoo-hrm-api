<?php

// app/Models/sql/Get.php
namespace App\Models\Resource\user;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class model_user extends Model
{
    public function list_roles()
    {
  

        $users = DB::select("SELECT * FROM roles");

        if (empty($users)) return [];

        return $users; 
    }
    public function list_user()
    {
  

        $users = DB::select("SELECT * FROM users");

        if (empty($users)) return [];

        foreach ($users as &$user) {
            $roles = DB::selectOne("SELECT * FROM roles WHERE id_roles  = :id_roles ", [
                'id_roles' => $user->role
            ]);
            $user->roles_info = $roles;
        }

        foreach ($users as &$user) {
            $department = DB::selectOne("SELECT * FROM agency WHERE id  = :agency_id ", [
                'agency_id' => $user->department
            ]);
            $user->department_info = $department;
        }

        return $users; 
    }

    public function view_user($id)
    {
        if ($id === null) {
           return null;
        }
        $users = DB::selectOne("SELECT * FROM users WHERE id = :id", [
        'id' => $id
        ]);
        if (empty($users)) {
            return null;
        }else {
   
         $roles = DB::selectOne("SELECT * FROM roles WHERE id_roles  = :id_roles ", [
                'id_roles' => $users->role
            ]);
            $users->roles_info = $roles;

          $department = DB::selectOne("SELECT * FROM agency WHERE id  = :agency_id ", [
                'agency_id' => $users->department
            ]);
            $users->department_info = $department;
        }

   
        return $users; 
    }

    public function Add_user($request)
    {
        $name = $request->input('name');
        $phone = $request->input('phone');
        $email = $request->input('email');
        $passwordInput = $request->input('password');
        $department = $request->input('department');
        $role = $request->input('role');

        // ตรวจสอบข้อมูลที่จำเป็น
        if (empty($name) || empty($email) || empty($passwordInput) || empty($role) || empty($phone) || empty($department)) {
            return false; // ข้อมูลไม่ครบ
        }

        // ตรวจสอบว่า username ซ้ำหรือไม่
        $check = DB::select("SELECT * FROM users WHERE username = ?", [$email]);

        if (!empty($check)) {
            return 'duplicate'; // username ซ้ำ
        }

        // เข้ารหัสรหัสผ่าน
        $password = password_hash($passwordInput, PASSWORD_DEFAULT);

        // เพิ่มข้อมูลผู้ใช้ใหม่
        DB::insert("INSERT INTO users (name, phone, username, password, department, role) VALUES (?, ?, ?, ?, ?, ?)", [
            $name,
            $phone,
            $email,
            $password,
            $department,
            $role
        ]);

        return true; // เพิ่มสำเร็จ
    }
 public function Update_user($request, $id)
{
    $name = $request->input('name');
    $phone = $request->input('phone');
    $email = $request->input('email');
    $department = $request->input('department');
    $role = $request->input('role');

    // ตรวจสอบข้อมูลที่จำเป็น
    if (empty($name) || empty($email) || empty($role) || empty($phone) || empty($department)) {
        return false; // ข้อมูลไม่ครบ
    }


    // อัปเดตข้อมูลผู้ใช้
    $result = DB::update(
        "UPDATE `users` 
         SET `name` = ?, 
             `phone` = ?, 
             `username` = ?, 
             `department` = ?, 
             `role` = ? 
         WHERE `id` = ?",
        [
            $name,
            $phone,
            $email,
            $department,
            $role,
            $id
        ]
    );

    return $result > 0; // true ถ้าอัปเดตสำเร็จ
}

public function Update_password($id, $newPassword)
{
    $password = $newPassword->input('password');
    if (empty($password)) {
        return false; // ไม่กรอกรหัสผ่านใหม่
    }

    // เข้ารหัสรหัสผ่านใหม่
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // อัปเดตรหัสผ่าน
    $result = DB::update(
        "UPDATE `users` 
         SET `password` = ? 
         WHERE `id` = ?",
        [
            $hashedPassword,
            $id
        ]
    );

    return $result > 0;
}




}
