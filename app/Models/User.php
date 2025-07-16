<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class User extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $table = 'users'; // ชื่อตาราง

    protected $fillable = [
        'name',
        'phone',
        'username',
        'password',
        'department',
        'role',
        'token_line',
        'img'
    ];

    protected $hidden = ['password']; // ซ่อนตอน response JSON

    public $timestamps = false; // ถ้าไม่มี created_at / updated_at
}
