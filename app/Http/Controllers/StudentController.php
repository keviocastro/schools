<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Transformers\StudentGradeTransformer;
use App\SchoolCalendar;
use App\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class StudentController extends Controller
{
    /**
     * Listagem do recurso de restudantes
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->parseMultiple(new Student);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $queryParams = Input::get();

        return $this->apiHandler
            ->parseSingle(New Student, $id, array_merge(['_with' => 'person'], $queryParams))
            ->getResult();
    }

    /**
     * Resumo anual do aluno, com notas e frequências
     * 
     * @param  Request $request 
     * @return \Illuminate\Http\Response
     */
    public function annualSummary(Request $request, $student_id)
    { 
        $this->validationForListAction([
                'school_calendar_id' => 'required|exists:school_calendars,id',
            ]);

        $schoolCalendar = SchoolCalendar::
            find($request->input('school_calendar_id'));

        $student = Student::findOrFail($student_id);

        return $student->annualSummary($schoolCalendar);
    }

    /**
     * Relatório anual do aluno, contendo
     * notas do aluno no ano agrupado por disciplina e fase do ano,
     * faltas do aluno no ano,
     * fases do ano letivo
     * disciplinas do aluno letivo
     * 
     * 
     * @param  Request $request    
     * @param  integer  $student_id
     * 
     * @return \Illuminate\Http\Response
     */
    public function annualReport(Request $request, $student_id)
    {
        $this->validationForListAction([
                'school_calendar_id' => 'required|exists:school_calendars,id',
            ]);

        $school_calendar_id = $request->input('school_calendar_id');

        $schoolCalendar = SchoolCalendar::findOrFail($school_calendar_id);
        $student = Student::findOrFail($student_id);

        $result['report_by_subjects'] = $student->averagesAndAbsencesInTheYear($schoolCalendar);

        return $result;
    }

}
