<?php

namespace App\Models\Resource\agency;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class model_agency extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $table = 'agency'; // ชื่อตาราง

    protected $fillable = [
        'id',
        'name',
        'detail'
    ];
     public $timestamps = false;
}
