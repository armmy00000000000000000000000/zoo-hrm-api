<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'departments';

    protected $fillable = ['id', 'name'];

    // protected $visible = ['id', 'name']; // ✅ บอกให้แสดงเฉพาะตอน toArray()/toJson()

    public $timestamps = false;
}

