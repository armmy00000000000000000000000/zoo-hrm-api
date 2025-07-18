<?php

namespace App\Models\Resource\agency;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class model_agency extends Model 
{

    public function getAll_agency()
    {
        $agencies = DB::select("SELECT * FROM agency");

        if (empty($agencies)) return [];

        return $agencies; 
    }

    public function Addagency($request)
    {
        $name = $request->input('name');
        $detail = $request->input('detail');

        if (empty($name) || empty($detail)) {
            return false; // ข้อมูลไม่ครบ
        }

        $checkDuplicate = DB::selectOne("SELECT * FROM agency WHERE name = :name", [
            'name' => $name
        ]);
        if ($checkDuplicate) {
            return 'duplicate'; // ชื่อหน่วยงานนี้ถูกใช้ไปแล้ว
        }

        // บันทึกข้อมูลหน่วยงานใหม่
        $result = DB::insert(
            "INSERT INTO `agency` (`name`, `detail`) VALUES (?, ?)",
            [
                $name,
                $detail
            ]
        );

        return $result ? true : false;
    }

    public function getID_agency($id)
    {
        $agency = DB::selectOne("SELECT * FROM agency WHERE id = :id", [
            'id' => $id
        ]);

        if (empty($agency)) return [];

        $division = DB::select("SELECT * FROM `division` WHERE `agency_id` = :agency_id", [
            'agency_id' => $agency->id
        ]);
        $agency->division = $division;

        foreach ($agency->division as $div) {
            $department = DB::select("SELECT * FROM `department` WHERE `division_id` = :division_id", [
                'division_id' => $div->division_id
            ]);
            $div->department = $department;
        }
        return $agency; 
    }
  

    public function Add_division($request)
    {
        $division_name = $request->input('division_name');
        $agency_id = $request->input('agency_id');

        if (empty($division_name) || empty($agency_id)) {
            return false; // ข้อมูลไม่ครบ
        }

        $checkDuplicate = DB::selectOne("SELECT * FROM division WHERE division_name = :division_name AND agency_id = :agency_id", [
            'division_name' => $division_name,
            'agency_id' => $agency_id
        ]);
        if ($checkDuplicate) {
            return 'duplicate'; // ชื่อหน่วยงานนี้ถูกใช้ไปแล้ว
        }

        // บันทึกข้อมูลหน่วยงานใหม่
        $result = DB::insert(
            "INSERT INTO `division` (`division_name`, `agency_id`) VALUES (?, ?)",
            [
                $division_name,
                $agency_id
            ]
        );

        return $result ? true : false;
    
    
    }

        public function Add_department($request)
    {
        $department_name = $request->input('department_name');
        $division_id = $request->input('division_id');

        if (empty($department_name) || empty($division_id)) {
            return false; // ข้อมูลไม่ครบ
        }

        $checkDuplicate = DB::selectOne("SELECT * FROM department WHERE department_name = :department_name AND division_id = :division_id", [
            'department_name' => $department_name,
            'division_id' => $division_id
        ]);
        if ($checkDuplicate) {
            return 'duplicate'; // ชื่อหน่วยงานนี้ถูกใช้ไปแล้ว
        }

        // บันทึกข้อมูลหน่วยงานใหม่
        $result = DB::insert(
            "INSERT INTO `department` (`department_name`, `division_id`) VALUES (?, ?)",
            [
                $department_name,
                $division_id
            ]
        );

        return $result ? true : false;
    
        
    }

    public function Edit_division($request, $id)
    {
        $division_name = $request->input('division_name');
    
        if (empty($division_name)) {
            return false; // ข้อมูลไม่ครบ
        }
        $editDivision = DB::update(
            "UPDATE `division` SET `division_name` = ? WHERE `division_id` = ?",
            [
                $division_name,
                $id
            ]
        );
        return $editDivision ? true : false;

    }
    public function Edit_department($request, $id)
    {
        $department_name = $request->input('department_name');
    
        if (empty($department_name)) {
            return false; // ข้อมูลไม่ครบ
        }
        $editDepartment = DB::update(
            "UPDATE `department` SET `department_name` = ? WHERE `department_id` = ?",
            [
                $department_name,
                $id
            ]
        );
        return $editDepartment ? true : false;

    }

  public function Delete_division($id)
    {
        // ลบข้อมูลที่เกี่ยวข้องในตาราง department ก่อน
        DB::delete("DELETE FROM department WHERE division_id = :division_id", [
            'division_id' => $id
        ]);

        // แล้วค่อยลบ division
        $deleteDivision = DB::delete("DELETE FROM division WHERE division_id = :id", [
            'id' => $id
        ]);

        return $deleteDivision ? true : false;
    }



}
