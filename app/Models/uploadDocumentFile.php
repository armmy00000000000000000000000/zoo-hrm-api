<?php


namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class uploadDocumentFile extends Model 
{
  public function uploadDocumentFile($request, $inputName = 'file')
{
    if (!$request->hasFile($inputName)) {
        return response()->json(['message' => 'ไม่พบไฟล์ที่อัปโหลด'], 400);
    }

    $file = $request->file($inputName);

    // ตรวจสอบประเภทไฟล์ที่อนุญาต
    $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
    $extension = strtolower($file->getClientOriginalExtension());

    if (!in_array($extension, $allowed)) {
        return response()->json(['message' => 'ไม่อนุญาตให้อัปโหลดไฟล์ประเภทนี้'], 422);
    }

    // ตั้งชื่อไฟล์ใหม่เพื่อกันซ้ำ
    $filename = time() . '_' . uniqid() . '.' . $extension;

    // ย้ายไฟล์ไปไว้ใน public/jobfile
    $uploadPath = base_path('public/jobfile');
    if (!file_exists($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }

    $file->move($uploadPath, $filename);

    // ส่ง URL กลับ
    return url('jobfile/' . $filename);
}

}