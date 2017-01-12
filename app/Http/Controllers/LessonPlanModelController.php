<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\LessonPlanModel;

class LessonPlanModelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = $this->apiHandler->parseMultiple(new LessonPlanModel);
        
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
                'definition' => 'required|array',
            ]);
        $lessonPlanModel = LessonPlanModel::create($request->all());
        return $this->response->created("/lesson-plan-models/{$lessonPlanModel->id}", $lessonPlanModel);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return LessonPlanModel::findOrFail($id);
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
                'definition' => 'required|array',
            ]);

        $lessonPlanModel = LessonPlanModel::findOrFail($id);
        $lessonPlanModel->update($request->all());
        // dd('validou');

        return $lessonPlanModel;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $lessonPlanModel = LessonPlanModel::findOrFail($id);
        $lessonPlanModel->delete();

        return $this->response->noContent();
    }
}
