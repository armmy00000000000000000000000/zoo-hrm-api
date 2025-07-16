<?php

// app/Models/sql/user/Post.php

namespace App\Models\sql\user;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Put extends Model
{
 

    public function Updatedata($data)
    {
        $id         = $data->input('id'); // รับ ID จากข้อมูล
        $name       = $data->input('name');
        $phone      = $data->input('phone');
        $username   = $data->input('username');
        $password   = $data->input('password');
        $department = $data->input('department');
        $role       = $data->input('role');
        $token_line = $data->input('token_line');
        $img        = $data->input('img');

        // ตรวจสอบ username ซ้ำ
        $user = DB::selectOne("SELECT * FROM users WHERE  id = :id", [
               'id' => $id
        ]);
        if (!$user) {
            return 'duplicate'; 
        }

        // อัปเดตข้อมูลผู้ใช้
        $update = DB::update("
            UPDATE users 
            SET name = :name, phone = :phone, username = :username, password = :password, 
                department = :department, role = :role, token_line = :token_line, img = :img
            WHERE id = :id
        ", [
            'id'         => $id,
            'name'       => $name,
            'phone'      => $phone,
            'username'   => $username,
            'password'   => password_hash($password, PASSWORD_DEFAULT),
            'department' => $department,
            'role'       => $role,
            'token_line' => $token_line,
            'img'        => $img
        ]);

        return $update ? true : false;
    }


}
