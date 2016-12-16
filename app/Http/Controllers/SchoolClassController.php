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
            ->getResult();
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
     * Relatório annual da turma, com notas e faltas dos alunos
     * 
     * @param  Request $request 
     * @param  int     $id      
     * 
     * @return \Illuminate\Http\Response          
     */
    public function annualReport(Request $request, $school_class_id, $subject_id)
    {
        $schoolClass = SchoolClass::findOrFail($school_class_id);
        $subject = Subject::findOrFail($subject_id);

        $students = $schoolClass->students;
        $students->transform(function($student, $key)
            use ($schoolClass, $subject){
            
            $student->annual_report = $student
                ->averagesAndAbsencesInTheYear($schoolClass->schoolCalendar, $subject);
            return $student;
        });

        return $this->collection($students, new AnnualReportTransformer);
    }
}
