<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\SchoolCalendar;

/**
 * `School Calendar` é considerado nessa api como a definição do ano letivo,
 * contendo fases fases avaliativas do ano (Ex.: bimestres), feriados, férias,
 * inicio e fim do ano.
 *
 * @Resource("SchoolCalendar", uri="/school-calendars")
 *
 */
class SchoolCalendarController extends Controller
{
    /**
     * Mostar todos os calendarios escolares
     * 
     * @Get("/")
     * @Versions({"v1"})
     * 
     * @Request(headers={"authorization": "Bearer <token_id>"}),
     * @Response(200, body={"year": 2016, "start": "2016-01-22", "end": "2016-12-20"}),
     * 
     */
    public function index()
    {   
        $result = $this->apiHandler->parseMultiple(new SchoolCalendar);
        
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
                // '{attribute}' => '{validation}',
            ]);
        $schoolCalendarController = SchoolCalendar::create($request->all());

        return $this->response->created("/resource/{$schoolCalendarController->id}", $schoolCalendarController);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->apiHandler
            ->parseSingle(New SchoolCalendar, $id)
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
            // 'attribute' => 'rule',
            ]);

        $schoolCalendarController = SchoolCalendar::findOrFail($id);
        $schoolCalendarController->update($request->all());

        return $schoolCalendarController;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $schoolCalendarController = SchoolCalendar::findOrFail($id);
        $schoolCalendarController->delete();

        return $this->response->noContent();
    }
}
