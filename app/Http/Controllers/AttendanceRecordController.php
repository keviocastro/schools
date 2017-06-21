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
        return $this->parseMultiple(new AttendanceRecord);        
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
            'absence_dismissal' => 'string|nullable'
            ], '', true);

        $records = $this->makeMultipleInputData();
        $attendanceRecords = [];

        DB::transaction(function() use ($records, &$attendanceRecords){
            foreach ($records as $key => $record) {
                $currentRecord = AttendanceRecord::
                            where('lesson_id', '=', $record['lesson_id'])
                            ->where('student_id', '=', $record['student_id'])
                            ->first();

                // O registro de frequencia do aluno é unico para uma aula,
                // então se o registro já existir ele é atualizado e não é criado um novo.
                if ($currentRecord) {
                    $currentRecord->update([
                            'presence' => $record['presence'], 
                            'absence_dismissal' => empty($record['absence_dismissal']) ? '' : $record['absence_dismissal']  
                    ]); // Não é permitido alterar outros atributos
                    $currentRecord->fresh();
                    $currentRecord->appliedAction = 'updated';
                    array_push($attendanceRecords, $currentRecord);
                }else{
                    $newRecord = AttendanceRecord::create($record);
                    $newRecord->appliedAction = 'created';
                    array_push($attendanceRecords, $newRecord);
                }                
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
        return $result->getResultOrFail();
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
            'presence' => 'integer|in:0,1,2',
            ]);

        $attendanceRecord = AttendanceRecord::findOrFail($id);
        $attendanceRecord->update($request->only('presence', 'absence_dismissal'));

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
