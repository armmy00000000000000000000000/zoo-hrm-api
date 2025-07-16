<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Response\Response;  // import Response class ให้ถูกต้อง
use App\Models\sql\login\Login;


class LoginController extends Controller
{
    protected $response;

    public function __construct()
    {
        $this->response = new Response(); 
    }


    public function Login(Request $request)
    {
        $model = new Login();
        $result = $model->LoginData($request);


        if ($result === 'invalid_password') {
            return $this->response->error('ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง', 401);
        } elseif ($result === 'not_found') {
            return $this->response->error('บัญชีผู้ใช้ไม่มีในระบบ', 403);
        } elseif ($result === 'empty') {
            return $this->response->error('กรอกข้อมูลไม่ครบ', 404);
        }else{
             return $this->response->success($result, 'เข้าสู่ระบบสำเร็จ', 201);;
        }
     
}



 
}
