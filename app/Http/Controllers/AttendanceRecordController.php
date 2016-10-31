<?php

namespace App\Http\Controllers;

use App\AttendanceRecord;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
     * Permite criar varios registros em uma mesma requisição
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validationForStoreAction($request, [
            'lesson_id' => 'required|exists:lessons,id',
            'student_id' => 'required|exists:students,id',
            'presence' => 'required|integer|in:0,1,2',
            ], '', true);

        $records = $this->makeMultipleInputData();
        $attendanceRecords = [];

        DB::transaction(function() use ($records, &$attendanceRecords){
            foreach ($records as $key => $record) {
                $currentRecord = AttendanceRecord::
                            where('lesson_id', '=', $record['lesson_id'])
                            ->where('student_id', '=', $record['student_id'])
                            ->first();

                if ($currentRecord) {
                    throw new ConflictHttpException("The record of the student (student.id = {$record['student_id']} ) to the lesson already exists.");
                }
                
                array_push($attendanceRecords, AttendanceRecord::create($record));
            }

        });

        
        if ($this->checkMultipleInputData()) {
            return $this->response->created(null, ['attendance_records' => $attendanceRecords]);
        }else{
            return $this->response->created('attendance_records/{$attendanceRecords[0]->id}', $attendanceRecords[0]);
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
            'presence' => 'integer|in:0,1',
            ]);

        $attendanceRecord = AttendanceRecord::findOrFail($id);
        $attendanceRecord->update($request->only('presence'));

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
