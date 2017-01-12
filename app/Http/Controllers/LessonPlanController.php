<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\LessonPlan;

class LessonPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = $this->apiHandler->parseMultiple(new LessonPlan);
        
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
            'start_date' => 'required|date_format:Y-m-d|dateLessOrEquals:end_date',
            'end_date' => 'required|date_format:Y-m-d',
            'lesson_plan_template_id' => 'exists:lesson_plan_models,id',
            'content' => 'required|array',
        ]);

        $lessonPlanController = LessonPlan::create($request->all());

        return $this->response->created("/resource/{$lessonPlanController->id}", $lessonPlanController);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return LessonPlan::findOrFail($id);
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
        $this->validationForStoreAction($request, [
            'start_date' => 'date_format:Y-m-d|dateLessOrEquals:end_date',
            'end_date' => 'date_format:Y-m-d',
            'lesson_plan_template_id' => 'exists:lesson_plan_models,id',
            'content' => 'array',
        ]);

        $lessonPlanController = LessonPlan::findOrFail($id);
        $lessonPlanController->update($request->all());

        return $lessonPlanController;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $lessonPlanController = LessonPlan::findOrFail($id);
        $lessonPlanController->delete();

        return $this->response->noContent();
    }
}
