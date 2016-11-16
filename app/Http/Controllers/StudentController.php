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
        $this->validationForListAction([
                'school_calendar_id' => 'required|exists:school_calendars,id',
                'school_calendar_phase_id' => 'exists:school_calendar_phases,id'
            ]);

        $school_calendar_id = $request->input('school_calendar_id');
        $school_calendar_phase_id = $request->input('school_calendar_phase_id', false);

        $student = Student::findOrFail($student_id);

        return $student->annualSummary($school_calendar_id, 
            $school_calendar_phase_id);
    }

    public function annualReport(Request $request, $student_id)
    {
        $this->validationForListAction([
                'school_calendar_id' => 'required|exists:school_calendars,id',
            ]);

        $school_calendar_id = $request->input('school_calendar_id');

        $schoolCalendar = SchoolCalendar::findOrFail($school_calendar_id);
        $student = Student::findOrFail($student_id);

        $result['subjects'] = $student->subjectsYear($school_calendar_id)
            ->get();
        
        $result['school_calendar_phases'] = $schoolCalendar->schoolCalendarPhases;

        $result['absences'] = $student->totalAbsencesYearPerSubject($school_calendar_id)->get();

        $grades = $student->studentGrades()
            ->join('assessments',
                'assessments.id',
                '=',
                'student_grades.assessment_id')
            ->get();

        $grouped = $grades->groupBy(function($item, $key) use ($grades){
            return $item->school_calendar_phase_id.'-'.$item->subject_id; 
        });

        $formated = [];
        $grouped->each(function($item, $key) use (&$formated){
            $ids = explode('-',$key);
            array_push($formated, [
                    'school_calendar_phase_id' => $ids[0],
                    'subject_id' => $ids[1],
                    'assessments' => $item->toArray()
                ]);
        });

        $result['student_grades'] = $formated;

        return $result;
    }

}
