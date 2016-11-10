<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\SchoolCalendar;
use App\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Listagem do recurso de restudantes
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = $this->apiHandler->parseMultiple(new Student);

        return $result->getBuilder()->paginate();
    }

    /**
     * Resumo anual do aluno, com notas e frequências
     * 
     * @param  Request $request 
     * @return \Illuminate\Http\Response
     */
    public function annualSummary(Request $request, $student_id)
    {
        $school_calendar_id = $request->input('school_calendar_id');
        $school_calendar_phase_id = $request->input('school_calendar_phase_id', false);

        $student = Student::findOrFail($student_id);

        return $student->annualSummary($school_calendar_id, 
            $school_calendar_phase_id);
    }

}
