<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Lesson;
use App\LessonPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'lesson_plan_template_id' => 'exists:lesson_plan_models,id',
            'content' => 'required|array',
            'lesson_ids' => 'array|exists:lessons,id',
        ]);

        $lessonPlan = null;
        DB::transaction(function() use ($request, &$lessonPlan){
            
            $lessonPlan = LessonPlan::create($request->all());
            $lessonsId = $request->get('lesson_ids');
            
            if ($lessonsId) {
                // is_array Porque é aceito lesson_ids = 1 ou lesson_ids = [1,2,3,...]
                $lessonsId = is_array($lessonsId) ? $lessonsId : [$lessonsId];
                Lesson::whereIn('id', $lessonsId)->update([
                        'lesson_plan_id' => $lessonPlan->id
                    ]);
                $lessonPlan->load('lessons');
            }
        });

        return $this->response->created("/resource/{$lessonPlan->id}", $lessonPlan);
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
            'lesson_plan_template_id' => 'exists:lesson_plan_models,id',
            'content' => 'array',
            'lesson_ids' => 'array|exists:lessons,id'
        ]);

        $lessonPlan = LessonPlan::findOrFail($id);
        DB::transaction(function() use ($lessonPlan, $request){
            $lessonPlan->update($request->all());

            $lessonsId = $request->get('lesson_ids');
            if ($lessonsId) {

                // Desassocia as aulas que estavam relacionadas ao plano 
                $lessonPlan->lessons()->update(['lesson_plan_id' => null]);

                // Associa as aulas do parametro $lessonsId ao plano
                $lessonsId = is_array($lessonsId) ? $lessonsId : [$lessonsId];
                Lesson::whereIn('id', $lessonsId)->update([
                        'lesson_plan_id' => $lessonPlan->id
                    ]);
            }
        });

        $lessonPlan->load('lessons');

        return $lessonPlan;
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
