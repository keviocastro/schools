<?php

namespace App\Http\Controllers;

use App\AttendanceRecord;
use App\Http\Requests;
use Illuminate\Http\Request;

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
            'student_id' => 'required|exists:students,id',
            'presence' => 'required|integer|in:0,1',
            ]);
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
        return AttendanceRecord::findOrFail($id);
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
            'student_id' => 'exists:students,id',
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
        //
    }
}
