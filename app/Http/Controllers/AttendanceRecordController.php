<?php

namespace App\Http\Controllers;

use App\AttendanceRecord;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class AttendanceRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = $this->apiHandler->parseMultiple(new AttendanceRecord);
        
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
            'lesson_id' => 'required|exists:lessons,id',
            'school_class_student_id' => 'required|exists:school_class_students,student_id',
            'presence' => 'required|integer|in:0,1',
            ]);

        $currentRecord = AttendanceRecord::
                    where('lesson_id', '=', Input::get('lesson_id'))
                    ->where('school_class_student_id', '=', Input::get('school_class_student_id'))
                    ->first();

        if ($currentRecord) {
            throw new ConflictHttpException('The record of the student to the lesson already exists.');
        }

        $attendanceRecord = AttendanceRecord::create($request->all());

        return $this->response->created("/attendance-records/{$attendanceRecord->id}", $attendanceRecord);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $result = $this->apiHandler->parseSingle(new AttendanceRecord, $id);
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
            'lesson_id' => 'exists:lessons,id',
            'school_class_student_id' => 'exists:school_class_students,student_id',
            'presence' => 'integer|in:0,1',
            ]);

        $attendanceRecord = AttendanceRecord::findOrFail($id);
        $attendanceRecord->update($request->all());

        return $attendanceRecord;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $attendanceRecord = AttendanceRecord::findOrFail($id);
        $attendanceRecord->delete();

        return $this->response->noContent();
    }
}
