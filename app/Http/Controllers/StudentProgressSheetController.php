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
    // public function store(Request $request)
    // {
    //     $this->validationForStoreAction($request, [
    //             // '{attribute}' => '{validation}',
    //         ]);
    //     $studentProgressSheetController = StudentProgressSheet::create($request->all());

    //     return $this->response->created("/resource/{$studentProgressSheetController->id}", $studentProgressSheetController);
    // }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function show($id)
    // {
    //     return StudentProgressSheet::findOrFail($id);
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, $id)
    // {
    //     $this->validationForUpdateAction($request, [
    //         // 'attribute' => 'rule',
    //         ]);

    //     $studentProgressSheetController = StudentProgressSheet::findOrFail($id);
    //     $studentProgressSheetController->update($request->all());

    //     return $studentProgressSheetController;
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy($id)
    // {
    //     $studentProgressSheetController = StudentProgressSheet::findOrFail($id);
    //     $studentProgressSheetController->delete();

    //     return $this->response->noContent();
    // }
}
