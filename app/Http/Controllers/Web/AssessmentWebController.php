<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class AssessmentWebController extends Controller
{
    public function index()
    {
        return view('umkm.assessment');
    }

    public function result()
    {
        return view('umkm.assessment_result');
    }
}