<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['message' => 'ไม่พบไฟล์ที่อัปโหลด'], 400);
        }

        $file = $request->file('file');

        // ตรวจสอบประเภทไฟล์ที่อนุญาต
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, $allowed)) {
            return response()->json(['message' => 'ไม่อนุญาตให้อัปโหลดไฟล์ประเภทนี้'], 422);
        }

        // ตั้งชื่อไฟล์ใหม่เพื่อกันซ้ำ
        $filename = time() . '_' . uniqid() . '.' . $extension;

        // ย้ายไฟล์ไปไว้ใน public/uploads
        $uploadPath = base_path('public/uploads');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $file->move($uploadPath, $filename);

        // ส่ง URL กลับ
        $url = url('uploads/' . $filename);

        return response()->json([
            'message' => 'อัปโหลดสำเร็จ',
            'filename' => $filename,
            'url' => $url
        ]);
    }
}
