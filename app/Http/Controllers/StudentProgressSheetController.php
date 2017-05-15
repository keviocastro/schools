<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\StudentProgressSheet;
use DB;

/**
 * Itens de resposta de fichas avaliativas do estudante
 */
class StudentProgressSheetController extends Controller
{
    /**
     * Listagem e pesquisa de itens
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->parseMultiple(new StudentProgressSheet);        
    }

    /**
     * Armazena itens
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validationForStoreAction($request, [
            'option_identifier' => 'string|nullable',
            'progress_sheet_item_id' => 'required|exists:progress_sheet_items,id',
            'student_id' => 'required|exists:students,id',
            'school_calendar_phase_id' => 'required|exists:school_calendar_phases,id',
            'school_class_id' => 'required|exists:school_classes,id',
            ], '', true);

        $records = $this->makeMultipleInputData();
        $items = [];

        DB::transaction(function() use ($records, &$items){
            foreach ($records as $key => $record) {
                array_push($items, StudentProgressSheet::create($record));
            }
        });

        if ($this->checkMultipleInputData()) {
            return $this->response->created(null, ['student_progress_sheets' => $items]);
        }else{
            return $this->response->created('student_progress_sheet/{$items[0]->id}', $items[0]);
        }
    
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validationForUpdateAction($request,[
            'option_identifier' => 'string|nullable',
            'progress_sheet_item_id' => 'required|exists:progress_sheet_items,id',
            'student_id' => 'required|exists:students,id',
            'school_calendar_phase_id' => 'required|exists:school_calendar_phases,id',
            'school_class_id' => 'required|exists:school_classes,id'
        ]);
        $assertation = StudentProgressSheet::findOrFail($id);
        $assertation->update($request->all());

        return $assertation;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $studentProgressSheet = StudentProgressSheet::findOrFail($id);
        $studentProgressSheet->delete();

        return $this->response->noContent();
    }
}
