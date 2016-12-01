<?php

namespace App\Http\Controllers;

use App\Assessment;
use App\Http\Requests;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->parseMultiple(new Assessment,['name']);
    }
}
