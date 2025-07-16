<?php

// app/Models/sql/user/Post.php

namespace App\Models\sql\user;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Delete extends Model
{
    public function DeleteData($id)
    {
        if ($id === null) {
            return false; // ถ้า ID เป็น null ให้คืนค่า false
        }

        // ลบผู้ใช้จากตาราง users
        $deleted = DB::delete("DELETE FROM users WHERE id = :id", [
            'id' => $id
        ]);

        return $deleted > 0; // คืนค่า true ถ้ามีการลบสำเร็จ
    }


}
