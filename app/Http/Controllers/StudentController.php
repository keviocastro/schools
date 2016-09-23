<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = $this->apiHandler->parseMultiple(new Student);

        return $result->getBuilder()->paginate();
    }

}
