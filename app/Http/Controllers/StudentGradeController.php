<?php

namespace App\Http\Controllers;

use App\Assessment;
use App\Http\Requests;
use App\SchoolClass;
use App\SchoolClassStudent;
use App\Student;
use App\StudentGrade;
use Exception;
use Illuminate\Http\Request;


class StudentGradeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = $this->apiHandler->parseMultiple(new StudentGrade);
        
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
            ], '',true);

        $records = $this->makeMultipleInputData();
        $StudentGrade = [];
        $campo = $request->toArray();
        $quantidae = count($campo);
        for($cont = 0 ; $cont < $quantidae ; $cont++){

            if(!empty($request->toArray()[$cont]))
                $lista = $campo[$cont];
            else
                $lista = $campo;


            $schoolClassId = $lista['school_class_id'];
            $studentId = $lista['student_id'];

            $schoolClass = SchoolClass::find($schoolClassId);
            $assessment  = Assessment::find($lista['assessment_id']);

            $calendar = $schoolClass->schoolCalendar->id;
            $calendarPhase = $assessment->schoolCalendarPhase->schoolCalendar->id;
            $consulta = SchoolClassStudent::where('school_class_id', $schoolClassId)
                ->where('student_id', $studentId)->get();

            if(!empty($consulta) && $calendar == $calendarPhase)
            {
                $StudentGrade[$cont] = StudentGrade::create($lista);
            }
            else
                return response('Student is not in this class or student not in a same year phase of grade.', 422);
        }
        
        if ($this->checkMultipleInputData()) {
            return $this->response->created(null, ['student_grades' => $StudentGrade]);
        }else{
            $Student = $StudentGrade[0];
            return $this->response->created('/StudentGrade/{$Student->id}', $Student);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $result = $this->apiHandler->parseSingle(new StudentGrade,$id);
        return $result->getResult();
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
            'grade' => 'required|numeric|max:10|min:0'
        ]);

        $StudentGrade = StudentGrade::findOrFail($id);

        $condicao = $request->student_id == $StudentGrade['student_id'] && 
            $request->subject_id == $StudentGrade['subject_id'] && 
            $request->assessment_id == $StudentGrade['assessment_id'] && 
            $request->school_class_id == $StudentGrade['school_class_id'];

        if($condicao)
        {
            // var_dump("entrou aqui");
            $StudentGrade->update($request->all());
        }
        else 
            return response('Only the grade can be changed.', 422);
        // dd($StudentGrade->toArray());
        return $StudentGrade;
    }
}
