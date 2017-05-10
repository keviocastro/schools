<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\StudentProgressSheet;

class StudentProgressSheetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = $this->parseMultiple(new StudentProgressSheet,['option_identifier','progress_sheet_item_id','student_id','school_calendar_phase_id']);
        
        return $result;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $studentProgressSheetController = StudentProgressSheet::create($request->all());

        return $this->response->created("/student-progress-sheets/{$studentProgressSheetController->id}", $studentProgressSheetController);
    }

    /**
     * Show a single resulte
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $result = $this->apiHandler->parseSingle(new StudentProgressSheet, $id);

        return $result->getResultOrFail();
    }


}
