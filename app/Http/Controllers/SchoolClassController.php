<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Transformers\AnnualReportTransformer;
use App\SchoolClass;
use App\Subject;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = $this->parseMultiple(new SchoolClass,['identifier']);
        return $result;
    }

    /**
     * Store a newly created school class in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validationForStoreAction($request, [
            'grade_id' => 'required|exists:grades,id',
            'shift_id' => 'required|exists:shifts,id',
            'identifier' => 'required|string',
            'progress_sheet_id' => 'nullable|exists:progress_sheets,id'
        ]);
        
        $schoolClass = SchoolClass::create($request->all());

        return $this->response->created("/schools/{$schoolClass->id}", $schoolClass);
    }

    /**
     * Display the school class resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->apiHandler
            ->parseSingle(New SchoolClass, $id)
            ->getResultOrFail();
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
        $this->validationForUpdateAction($request, [
            'identifier' => 'string',
            'grade_id' => 'exists:grades,id',
            'shift_id' => 'exists:shifts,id',
        ]);

        $schoolClass = SchoolClass::findOrFail($id);
        $schoolClass->update($request->all());

        return $schoolClass;
    }

    /**
     * Removes school class resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $schoolClass = SchoolClass::findOrFail($id);
        $schoolClass->delete();

        return $this->response->noContent();
    }

    /**
     * @todo Analisar e melhor desempenho dessa action
     * 
     * Relatório annual da turma, com notas e faltas dos alunos
     * 
     * @param  Request $request 
     * @param  int     $id      
     * 
     * @return \Illuminate\Http\Response          
     */
    public function annualReport(Request $request, $schoolClassId, $subject_id)
    {
        $schoolClass = SchoolClass::findOrFail($schoolClassId);
        $subject = Subject::findOrFail($subject_id);

        $queryStudents = $schoolClass->students()
          ->select('students.*')
          ->join('people', 'people.id', '=', 'students.person_id');
        
        if ($request->get('_sort_students') == 'name') {
            $queryStudents->orderBy('name');
        }

        if ($request->get('_sort_students') == '-name') {
            $queryStudents->orderBy('name', 'desc');
        }

        $students = $queryStudents->get();
        $students->transform(function($student, $key)
            use ($schoolClass, $subject){
            
            $student->annual_report = $student
                ->averagesAndAbsencesInTheYear($schoolClass->schoolCalendar, $subject);
            return $student;
        });

        $transform = new AnnualReportTransformer();
        $resource = $transform->transformCollection($students);
        
        return $this->response->array($resource);
    }

    /**
     * @todo o parametro index_by_id é um teste avaliar se é melhor consumir o array desta forma.  
     *      Avaliar isso com os desenvolvedores que utilizam a API e decidir se continua usando na api 
     *      ou não.
     * 
     * Lista de faltas da turma
     * 
     *
     * @param Request $request
     * @param int $schoolClassId
     * @return void
     */
    public function absences(Request $request, $schoolClassId){
        $indexById = $request->input('_index_by_id', false);
        $orderBy = $request->input('_sort', 'student_name');

        $schoolClass = SchoolClass::findOrFail($schoolClassId);
        $phases = $schoolClass->schoolCalendar->phases;
        $students = $schoolClass->students()
            ->join('people', 'people.id', '=' ,'students.person_id');
        
        if($orderBy == '-student_name'){
            $students->orderBy('people.name', 'desc');
        }elseif($orderBy == 'student_name'){
            $students->orderBy('people.name');
        }
        
        $students = $students->get();

        if($indexById) {
            return $this->listAbsencesIndexById($students, $phases);
        }

        return $this->listAbsences($students, $phases);
    }

    private function listAbsencesIndexById($students, $phases){
        $result = collect();
        foreach($students as $student){
            $absencesPerPhase = collect();
            foreach($phases as $phase){
                $absences = $student->queryAbsencesYearPhase($phase->id)
                    ->count();
                
                $absencesPerPhase->put($phase->id, [
                    'absences' => $absences
                    ]);
            }
            
            $result->put($student->id, $absencesPerPhase);
        }

        return $result; 
    }

    private function listAbsences($students, $phases){
        $result = [];
        foreach($students as $student){
            $absencesPerPhase = [];
            foreach($phases as $phase){
                $absences = $student->queryAbsencesYearPhase($phase->id)
                    ->count();

                array_push($absencesPerPhase, [
                    'school_calendar_phase_id' => $phase->id,
                    'absences' => $absences,
                    ]);
            }
            
            array_push($result, [
                'student_id' => $student->id,
                'student_name' => $student->person->name,
                'school_calendar_phases' => $absencesPerPhase
                ]);
        }

        return $result;
    }
}
