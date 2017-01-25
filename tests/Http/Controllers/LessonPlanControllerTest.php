<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LessonPlanControllerTest extends TestCase
{
    /**
     * @covers App\Http\Controllers\LessonPlanController::index
     *
     * @return void
     */
    public function testIndex()
    {
        $lessonPlan = factory(App\LessonPlan::class)->create();

        $this->get('api/lesson-plans?_sort=-id',
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($lessonPlan->toArray());
    }

    /**
     * @covers App\Http\Controllers\LessonPlanController::store
     *
     * Teste para fluxo principal de sucesso
     *
     * @return void
     */
    public function testStore()
    {
        $lessonPlan = factory(App\LessonPlan::class)->make()->toArray();

        $this->post('api/lesson-plans',
            $lessonPlan,
            $this->getAutHeader())
            ->assertResponseStatus(201)
            ->seeJson($lessonPlan);
    }

    /**
     * @covers App\Http\Controllers\LessonPlanController::store
     *
     * Teste para validações de parametros de entrada durante a criação de novo recurso
     *
     * @return void
     */
    public function testStoreValidation()
    {
        $lessonPlan = factory(App\LessonPlan::class)->make()->toArray();
        $lessonPlan['start_date'] = '2016-04-08';
        $lessonPlan['end_date'] = '2016-04-07';
        $lessonPlan['lesson_plan_template_id'] = -99;
        $lessonPlan['content'] = "Thnis is bot an array it's a string";

        $this->post('api/lesson-plans',
            $lessonPlan,
            $this->getAutHeader())
            ->assertResponseStatus(422)
            ->seeJsonEquals([
                    "message" => "Could not create new resource.",
                    'errors' => [
                        'lesson_plan_template_id' => [
                            'The selected lesson plan template id is invalid.'
                        ],
                        'content' => [
                            "The content must be an array."
                        ]
                    ],
                    "status_code" => 422
                ]);
    }

    /**
     * @covers App\Http\Controllers\LessonPlanController::show
     *
     * Teste para exibição do recurso
     *
     * @return void
     */
    public function testShow()
    {
        $lessonPlan = factory(App\LessonPlan::class)->create()->toArray();
        
        $this->get("api/lesson-plans/{$lessonPlan['id']}",
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJson($lessonPlan);
    }

    /**
     * @covers App\Http\Controllers\LessonPlanController::update
     *
     * Teste para atualização do recurso
     *
     * @return void
     */
    public function testUpdate()
    {
    	$lessonPlan = factory(App\LessonPlan::class)->create();
    	$lessonPlan_changed = factory(App\LessonPlan::class)->make()->toArray();

        $this->put("api/lesson-plans/{$lessonPlan->id}",
        	$lessonPlan_changed,
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($lessonPlan_changed);
    }

    /**
     * @covers App\Http\Controllers\LessonPlanController::store
     *
     * Teste para validações de parametros de entrada durante a atualização do recurso
     *
     * @return void
     */
    public function testUpdateValidation()
    {
        $lessonPlan = factory(App\LessonPlan::class)->make()->toArray();
        $lessonPlan['lesson_plan_template_id'] = -99;
        $lessonPlan['content'] = "Thnis is bot an array it's a string";

        $this->put('api/lesson-plans/{$lessonPlan->id}',
            $lessonPlan,
            $this->getAutHeader())
            ->assertResponseStatus(422)
            ->seeJsonEquals([
                "message" => "Could not create new resource.",
                'errors' => [
                    'lesson_plan_template_id' => [
                        'The selected lesson plan template id is invalid.'
                    ],
                    'content' => [
                        "The content must be an array."
                    ]
                ],
                "status_code" => 422
            ]);
    }

    /**
     * @covers App\Http\Controllers\LessonPlanController::destroy
     *
     * Teste par exclusão do recurso
     * 
     * @return void
     */
    public function testDestroy()
    {
        $lessonPlan = factory(\App\LessonPlan::class)->create();

        $this->delete("api/lesson-plans/{$lessonPlan->id}",
            [],
            $this->getAutHeader())
            ->assertResponseStatus(204)
            ->seeIsSoftDeletedInDatabase('lesson_plans', ['id' => $lessonPlan->id]);
    }
}
