<?php

// app/Models/sql/user/Post.php

namespace App\Models\sql\user;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public function Register($data)
    {
        $name       = $data->input('name');
        $phone      = $data->input('phone');
        $username   = $data->input('username');
        $password   = $data->input('password');
        $department = $data->input('department');
        $role       = $data->input('role');
        $token_line = $data->input('token_line');
        $img        = $data->input('img');

        // ตรวจสอบ username ซ้ำ
        $user = DB::selectOne("SELECT * FROM users WHERE username = :username", [
            'username' => $username
        ]);
        if ($user) {
            return 'duplicate'; //  return แบบจำแนก
        }

        // สมัครใหม่
        $insert = DB::insert("
            INSERT INTO users (name, phone, username, password, department, role, token_line, img)
            VALUES (:name, :phone, :username, :password, :department, :role, :token_line, :img)
        ", [
            'name'       => $name,
            'phone'      => $phone,
            'username'   => $username,
            'password'   => password_hash($password, PASSWORD_DEFAULT),
            'department' => $department,
            'role'       => $role,
            'token_line' => $token_line,
            'img'        => $img
        ]);

        return $insert ? true : false;
    }




}
