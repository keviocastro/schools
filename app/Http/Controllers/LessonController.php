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
        $result = $this->apiHandler->parseMultiple(new Lesson);
        
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
            'school_class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:shifts,id',
            'start' => 'required|date_format:Y-m-d H:i:s',
            'end' => 'required|date_format:Y-m-d H:i:s',
        ]);
        
        $lesson = Lesson::create($request->all());

        return $this->response->created("/lessons/{$lesson->id}", $lesson);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $result = $this->apiHandler->parseSingle(New Lesson, $id);
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
            'school_class_id' => 'exists:school_classes,id',
            'subject_id' => 'exists:shifts,id',
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listPerDay(Request $request)
    {

        $startDate = $request->input('start', Carbon::now()->format('Y-m-d'));
        $endDate = Carbon::parse($startDate)->addDays(15)->format('Y-m-d');

        $queryDaysBetweenDates = "select * from (select adddate('$startDate',t4*10000 + t3*1000 + t2*100 + t1*10 + t0) day from (select 0 t0 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0, (select 0 t1 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1, (select 0 t2 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2, (select 0 t3 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3, (select 0 t4 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v where day between '$startDate' and '$endDate'";

        $days = DB::select(DB::raw($queryDaysBetweenDates));

        $query = Lesson::
            rightJoin(
                DB::raw("($queryDaysBetweenDates) as dates"),
                'day', '=', DB::raw('DATE_FORMAT(lessons.start, "%Y-%m-%d")'));


        $result = $this->apiHandler->parseMultiple($query);
        $result = $result->getBuilder()->paginate()->toArray();
        $data = collect($result['data'])->groupBy('day')->sort()->toArray();
        

        foreach ($days as $key => $day) {
            $days[$key]->lessons = empty($data[$day->day][0]["id"]) ? [] :  $data[$day->day];
        }
        $result['data'] = $days;

        return $result;
    }
}
