<?php
// app/Models/sql/Login.php
namespace App\Models\sql\login;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class Login extends Model
{
    public function LoginData($data)
    {
        $username = $data->input('username');
        $password = $data->input('password');

        if (empty($username) || empty($password)) {
            return 'empty'; // กรณีไม่ได้กรอกข้อมูล
        }

        // ดึงข้อมูลผู้ใช้
        $user = DB::selectOne("SELECT * FROM users WHERE username = :username", [
            'username' => $username
        ]);

        if (!$user) {
            return 'not_found'; // ไม่พบผู้ใช้
        }

        // ตรวจสอบรหัสผ่าน
        if (!password_verify($password, $user->password)) {
            return 'invalid_password'; // รหัสผ่านไม่ถูกต้อง
        }

        // ดึงข้อมูลแผนก
        $department = DB::selectOne("SELECT * FROM agency WHERE id = :id", [
            'id' => $user->department
        ]);
        $user->department_info = $department;

        $roles = DB::selectOne("SELECT * FROM roles WHERE id_roles  = :id_roles", [
            'id_roles' => $user->role
        ]);
        $user->roles_info = $roles;

        // ลบ api key เดิมก่อนสร้างใหม่
        DB::table('api_keys')->where('user_id', $user->id)->delete();

        // สร้าง api_key ใหม่
        $token = 'PJV||' . Str::random(40);
        $expiration = Carbon::now()->addHours(8)->addMinutes(30);

        DB::table('api_keys')->insert([
            'token' => $token,
            'status' => 'active',
            'expiration_date' => $expiration,
            'user_id' => $user->id
        ]);

        $user->token = $token;
        $user->time_api = $expiration;

        // ดึงเมนูหลักทั้งหมด
        $menus = DB::select("
            SELECT me.id , me.menu_name, me.details, me.icons
            FROM menus me JOIN user_menu_permissions usper ON me.id = usper.menu_id WHERE usper.roles_id = :role_id
        ", ['role_id' => $user->role]
        );
    

        // วนลูปเพื่อดึง sub_menus ของแต่ละเมนู
        foreach ($menus as &$menu) {
            $sub_menus = DB::select("
                SELECT id, sub_menu_name, details
                FROM sub_menus
                WHERE menu_id = :menu_id
            ", ['menu_id' => $menu->id]);

            $menu->sub_menus = $sub_menus;
        }

        $user->user_menu_permissions = $menus;

        // ไม่ส่งคืน password
        unset($user->password);

        return $user; // ส่งข้อมูลผู้ใช้ทั้งหมด
    }
}
