<?php

namespace App\Http\Controllers;

use App\Assessment;
use App\Http\Requests;
use App\SchoolClass;
use App\SchoolClassStudent;
use App\Student;
use App\StudentGrades;
use Exception;
use Illuminate\Http\Request;


class StudentGradesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = $this->apiHandler->parseMultiple(new StudentGrades);
        
        return $result->getBuilder()->paginate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validationForStoreAction($request, [
            'grade' => 'required|numeric|max:10|min:0',
            'student_id' => 'required|numeric',
            'subject_id' => 'required|numeric',
            'assessment_id' => 'required|numeric',
            'owner_person_id' => 'required|numeric',
        ]);

        $schoolClassId = $request->input('school_class_id');
        $studentId = $request->input('student_id');

        $schoolClass = SchoolClass::find($schoolClassId);
        $assessment  = Assessment::find($request->input('assessment_id'));

        //dump($schoolClass->schoolCalendar->id);
        //dd($assessment->schoolCalendarPhase->schoolCalendar->id);
        $calendar = $schoolClass->schoolCalendar->id;
        $calendarPhase = $assessment->schoolCalendarPhase->schoolCalendar->id;

        $consulta = SchoolClassStudent::where('school_class_id', $schoolClassId)->where('student_id', $studentId)->get();
        
        if(!empty($consulta->toArray()) && $calendar == $calendarPhase)
        {
            $studentGrades = StudentGrades::create($request->all());
        }
        else
            return response('Student is not in this class or student not in a same year phase of grade.', 422);

        return $this->response->created("/studentGrades/{$studentGrades->id}", $studentGrades);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $result = $this->apiHandler->parseSingle(new StudentGrades,$id);
        return $result->getResult();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
            'grade' => 'required|numeric|max:10|min:0',
            'student_id' => 'required|numeric',
            'subject_id' => 'required|numeric',
            'assessment_id' => 'required|numeric',
            'owner_person_id' => 'required|numeric',
        ]);

        $studentGrades = StudentGrades::findOrFail($id);
        $studentGrades->update($request->all());

        return $studentGrades;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
