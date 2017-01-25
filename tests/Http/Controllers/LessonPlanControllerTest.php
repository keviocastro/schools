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
        // Cria um plano de aula relacionando a 2 aulas que já existem
        $lessonPlan = factory(App\LessonPlan::class)->make()->toArray();
        $lessons = factory(App\Lesson::class, 2)->create([
            'lesson_plan_id' => null
            ]);
        $lessonsIds = $lessons ->pluck('id')->toArray();
        $lessonPlan['lessons_id'] = $lessonsIds;

        // Formata o array da forma que é esperado o retorno
        $lessonPlanResult = $lessonPlan;
        unset($lessonPlanResult['lessons_id']);
        $lessonPlanResult['id'] = App\LessonPlan::orderBy('id', 'desc')->first()->id + 1; 
        $lessons[0]['lesson_plan_id'] = $lessonPlanResult['id'];
        $lessons[1]['lesson_plan_id'] = $lessonPlanResult['id'];

        // Experado retornar o plano de aula e as aulas relacionada. 
        // parametro `lessons_id`
        $result = array_merge(
            $lessonPlanResult, 
            ['lessons' => $lessons->toArray()]
        );
        
        $this->post('api/lesson-plans',
            $lessonPlan,
            $this->getAutHeader())
            ->assertResponseStatus(201)
            ->seeJsonEquals(['lesson_plan' => $result]);
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
        $lessonPlan['lessons_id'] = [-1,-2];

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
                        ],
                        'lessons_id' => [
                            'The selected lessons id is invalid.'
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
    	$lessons = factory(App\Lesson::class, 2)->create([
                'lesson_plan_id' => $lessonPlan->id
            ]);

        $lessonPlan_changed = factory(App\LessonPlan::class)->make()->toArray();
        $newLesson = factory(App\Lesson::class)->create();
        $lessonPlan_changed['lessons_id'] = [$newLesson->id];

        // Nos resultados é esperado que a aula relacionada seja somente a que foi enviada
        $lessonPlanResult = $lessonPlan_changed;
        unset($lessonPlanResult['lessons_id']);
        $newLesson->lesson_plan_id = $lessonPlan->id;
        $lessonPlanResult['lessons'] = [$newLesson->toArray()];
        $lessonPlanResult['id'] = $lessonPlan->id;

        $this->put("api/lesson-plans/{$lessonPlan->id}",
        	$lessonPlan_changed,
        	$this->getAutHeader())->dump()
        	->assertResponseStatus(200)
        	->seeJsonEquals(['lesson_plan' => $lessonPlanResult]);
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
        $lessonPlan['lessons_id'] = [-1];

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
                    ],
                    'lessons_id' => [
                        'The selected lessons id is invalid.'
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
