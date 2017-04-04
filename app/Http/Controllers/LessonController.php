<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Lesson;
use Carbon\Carbon;
use DB;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->parseMultiple(new Lesson);
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
            'school_class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'start' => 'required|date_format:Y-m-d H:i:s',
            'end' => 'required|date_format:Y-m-d H:i:s',
        ]);
        
        $lesson = Lesson::create($request->all());

        return $this->response->created("/lessons/{$lesson->id}", $lesson);
    }

    /**
     * Display the specified resource.
     *
     * @todo Incluir parametros attach=students.last_ocurrences,students.attendance_record
     *       Refatorar sql para o model.
     *       Filtrar o resultados de faltas de acordo com o ano letivo da turma
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {   
        $result = $this->apiHandler->parseSingle(New Lesson, $id)->getResult();
        $attach = explode(',', $request->input('attach'));

        if (in_array('students',$attach) || 
            in_array('students.attendanceRecord',$attach) ||
            in_array('students.last_occurences',$attach)) {

           $students = $result->students();

            if (in_array('students.attendanceRecord',$attach)) {
                $students->map(function($item, $key) use ($result){
                    $item->attendance_record = \App\AttendanceRecord::
                        where('lesson_id', $result->id)
                        ->where('student_id', $item->id)
                        ->first();
                });
            }

            if (in_array('students.last_occurences',$attach)) {
                $students->map(function($item, $key){
                    $item->last_occurences = \App\Occurence::
                        where('about_person_id', $item->id)
                        ->orderBy('updated_at')
                        ->take(2)
                        ->with('level')
                        ->get();
                });
            }

            if (in_array('students.absenceSummary', $attach)) {
                $students->map(function($item, $key) use ($result){
                    $item->absence_summary = $item->absenceSummaryYear(
                            $result->school_class_id,
                            $result->subject_id,
                            $result->schoolClass->school_calendar_id
                        );
                });
            }

            $result['students'] = $students;

        }

        return $result;
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
            'school_class_id' => 'exists:school_classes,id',
            'subject_id' => 'exists:subjects,id',
            'start' => 'date_format:Y-m-d H:i:s',
            'end' => 'date_format:Y-m-d H:i:s',
        ]);

        $lesson = Lesson::findOrFail($id);
        $lesson->update($request->all());

        return $lesson;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->delete();

        return $this->response->noContent();
    }

    /**
     * @todo Converter sql para classe de modelo.
     *       Decidir se as datas serão dinamicas ou serão a partir da tabela dates.
     * 
     * @return \Illuminate\Http\Response
     */
    public function listPerDay(Request $request)
    {
        $this->validationForListAction([
                'start' => 'date_format:Y-m-d',
                'end' => 'date_format:Y-m-d',
                // ID do usuário no serviço de autentificação utilizado: auth0 por exemplo.
                'user_id' => 'string', 
            ]);

        $startDate = $request->input('start', Carbon::now()->format('Y-m-d'));
        $endDate = $request->input('end', Carbon::parse($startDate)->addDays(14)->format('Y-m-d'));
        $user_id = $request->input('user_id');

        $queryDaysBetweenDates = "select * from (select adddate('$startDate',t4*10000 + t3*1000 + t2*100 + t1*10 + t0) day from (select 0 t0 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0, (select 0 t1 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1, (select 0 t2 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2, (select 0 t3 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3, (select 0 t4 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v where day between '$startDate' and '$endDate'";

        $days = DB::select(DB::raw($queryDaysBetweenDates));

        $query = Lesson::select('lessons.*', 'dates.*')
            ->rightJoin(
                DB::raw("($queryDaysBetweenDates) as dates"),
                'day', '=', DB::raw('DATE_FORMAT(lessons.start, "%Y-%m-%d")'));

        if ($user_id) {
            $teacher = \App\Teacher::findByUserId($user_id);
            if ($teacher) {
                $query->where('lessons.teacher_id', $teacher->id);
            }
        }

        // apiHandler->parseMultiple para possibilitar utilizar o parametro _with da requisição
        $result = $this->apiHandler->parseMultiple($query, [], $request->except('start', 'end', 'user_id'));
        $data = $result->getResult()->toArray();

        // To group lessons per day
        $data = collect($data)->groupBy('day')->sort()->toArray();
        foreach ($days as $key => $day) {
            $days[$key]->lessons = empty($data[$day->day][0]["id"]) ? [] :  $data[$day->day];
        }
        
        return [
                'start' => $startDate,
                'end' => $endDate,
                'data' => $days
            ];
    }
}
