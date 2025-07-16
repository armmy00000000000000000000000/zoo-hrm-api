<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Getuser extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'users';

    protected $fillable = [
        'id', 'name', 'phone', 'role',
    ];
      // สร้างความสัมพันธ์กับตาราง departments
    public function departmentInfo()
    {
        return $this->belongsTo(Department::class, 'department', 'id');
    }

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public $timestamps = false;

}