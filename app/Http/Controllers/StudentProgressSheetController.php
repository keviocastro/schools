<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\StudentProgressSheet;
use App\SchoolClassStudent;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use DB;

/**
 * Itens de resposta de fichas avaliativas do estudante
 */
class StudentProgressSheetController extends Controller
{
    /**
     * Listagem e pesquisa de itens de ficha de avaliação de um aluno
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->parseMultiple(new StudentProgressSheet); 
    }


    /**
     * @todo  Migrar regras de negocio para o Repository.
     * 
     * Armazena itens de ficha de avaliação de um aluno
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

                /**
                 * O aluno pode ter somente um resposta de um item de avaliação, em uma fase do ano, para uma turma.
                 * Isso significa que um requisição com a combinação de parametros progress_sheet_item_id, school_calendar_phase_id,
                 * school_class_id e student_id, já existirem, um novo registro não será criado e sim atualizado.
                 *
                 * @link https://github.com/keviocastro/schools/issues/4
                 */
                $current_answer = StudentProgressSheet::where([
                    ['progress_sheet_item_id', '=', $record['progress_sheet_item_id']],
                    ['student_id', '=', $record['student_id']],
                    ['school_calendar_phase_id', '=', $record['school_calendar_phase_id']],
                    ['school_class_id', '=',$record['school_class_id']]
                ])->first();

                if ($current_answer){
                    $current_answer->update([
                            'option_identifier' => $record['option_identifier']]
                    );
                    $current_answer->appliedAction = 'updated';
                    array_push($items, $current_answer);
                }else{

                    $studentInClass = SchoolClassStudent::
                        where('school_class_id', $record['school_class_id'])
                        ->where('student_id', $record['student_id'])
                        ->first();

                    if ($studentInClass) {
                        $newRecord = StudentProgressSheet::create($record);
                        $newRecord->appliedAction = 'created';
                        array_push($items, $newRecord);
                    }else{
                        throw new ConflictHttpException(
                            "The student is not in the school class id ({$record['school_class_id']})."
                        );
                    }
                }

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
