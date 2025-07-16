<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class Auht extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $table = 'api_keys'; // ชื่อตาราง

    protected $fillable = [
        'token'
    ];
     public $timestamps = false;
}
