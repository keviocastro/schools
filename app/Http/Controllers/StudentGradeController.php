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
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;


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
            'subject_id' => 'required|numeric|exists:subjects,id',
            'assessment_id' => 'required|numeric|exists:assessments,id',
            'school_class_id' => 'required|numeric|exists:school_classes,id'
            ], '',true);

        $records = $this->makeMultipleInputData();

        $studentGradesCreated = collect();
        foreach ($records as $key => $grade) {

            $studentInClass = SchoolClassStudent::
                where('school_class_id', $grade['school_class_id'])
                ->where('student_id', $grade['student_id'])
                ->first();

            if (!$studentInClass) {
                throw new ConflictHttpException(
                    "The student is not in the school class ({$grade['school_class_id']})."
                    );
            }

            $schoolClass = SchoolClass::findOrFail($grade['school_class_id']);
            $assessment = Assessment::findOrFail($grade['assessment_id']);

            if ($schoolClass->school_calendar_id != 
                $assessment->schoolCalendarPhase->school_calendar_id) {
                
                throw new ConflictHttpException(
                    "Assessment does not belong to the same school year of the class."
                    );
            }

            $studentGradesCreated->push(StudentGrade::create($grade));
        }        

        if ($this->checkMultipleInputData()) {
            return $this->response->created(null, ['student_grades' => $studentGradesCreated]);
        }else{
            
            $created = $studentGradesCreated->first();
            return $this->response->created('/student-grades/{$created->id}', 
                $created);
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
            $StudentGrade->update($request->all());
        }else{
            throw new ConflictHttpException('Only the grade can be changed.');
        }

        return $StudentGrade;
    }
}
