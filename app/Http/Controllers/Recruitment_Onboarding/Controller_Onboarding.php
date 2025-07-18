<?php

namespace App\Http\Controllers\Recruitment_Onboarding;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Response\Response;  // import Response class ให้ถูกต้อง
use App\Models\Resource\agency\model_agency;
use App\Models\Resource\user\model_user;
use App\Models\Resource\roles\model_roled;
use App\Models\Resource\permission\model_permission;


class Controller_Onboarding extends Controller
{
    protected $response;

    public function __construct()
    {
        $this->response = new Response(); 
    }




    }



 

