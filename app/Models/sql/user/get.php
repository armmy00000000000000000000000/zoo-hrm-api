<?php

// app/Models/sql/Get.php
namespace App\Models\sql\user;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Get extends Model
{
    public function Getemproiye($id)
    {
        if ($id === null) return null;

        $users = DB::select("SELECT * FROM users WHERE department = :department", [
            'department' => $id
        ]);

        if (empty($users)) return [];

        foreach ($users as &$user) {
            $department = DB::selectOne("SELECT * FROM departments WHERE id = :id", [
                'id' => $user->department
            ]);
            $user->department_info = $department;
        }

        return $users; // ✅ ส่งข้อมูลกลับมาอย่างเดียว
    }

       public function GetuserID($id)
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
   
        $department = DB::selectOne("SELECT * FROM departments WHERE id = :id", [
            'id' => $users->department
        ]);
        $users->department_info = $department;
        }

   
        return $users; // ✅ ส่งข้อมูลกลับมาอย่างเดียว
    }
}
