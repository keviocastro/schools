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
        	->assertStatus(200)
        	->assertJsonFragment($lessonPlan->toArray());
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
        $lessonPlan['lesson_ids'] = $lessonsIds;

        // Formata o array da forma que é esperado o retorno
        $lessonPlanResult = $lessonPlan;
        unset($lessonPlanResult['lesson_ids']);
        $lessonPlanResult['id'] = App\LessonPlan::orderBy('id', 'desc')->first()->id + 1; 
        $lessons[0]['lesson_plan_id'] = $lessonPlanResult['id'];
        $lessons[1]['lesson_plan_id'] = $lessonPlanResult['id'];

        // Experado retornar o plano de aula e as aulas relacionada. 
        // parametro `lesson_ids`
        $result = array_merge(
            $lessonPlanResult, 
            ['lessons' => $lessons->toArray()]
        );
        
        $this->post('api/lesson-plans',
            $lessonPlan,
            $this->getAutHeader())
            ->assertStatus(201)
            ->assertExactJson(['lesson_plan' => $result]);
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
        $lessonPlan['lesson_ids'] = [-1,-2];

        $this->post('api/lesson-plans',
            $lessonPlan,
            $this->getAutHeader())
            ->assertStatus(422)
            ->assertExactJson([
                    "message" => "Could not create new resource.",
                    'errors' => [
                        'lesson_plan_template_id' => [
                            'The selected lesson plan template id is invalid.'
                        ],
                        'content' => [
                            "The content must be an array."
                        ],
                        'lesson_ids' => [
                            'The selected lesson ids is invalid.'
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
            ->assertStatus(200)
            ->assertJsonFragment($lessonPlan);
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
        $lessonPlan_changed['lesson_ids'] = [$newLesson->id];

        // Nos resultados é esperado que a aula relacionada seja somente a que foi enviada
        $lessonPlanResult = $lessonPlan_changed;
        unset($lessonPlanResult['lesson_ids']);
        $newLesson->lesson_plan_id = $lessonPlan->id;
        $lessonPlanResult['lessons'] = [$newLesson->toArray()];
        $lessonPlanResult['id'] = $lessonPlan->id;

        $this->put("api/lesson-plans/{$lessonPlan->id}",
        	$lessonPlan_changed,
        	$this->getAutHeader())
        	->assertStatus(200)
        	->assertExactJson(['lesson_plan' => $lessonPlanResult]);
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
        $lessonPlan['lesson_ids'] = [-1];

        $this->put('api/lesson-plans/{$lessonPlan->id}',
            $lessonPlan,
            $this->getAutHeader())
            ->assertStatus(422)
            ->assertExactJson([
                "message" => "Could not create new resource.",
                'errors' => [
                    'lesson_plan_template_id' => [
                        'The selected lesson plan template id is invalid.'
                    ],
                    'content' => [
                        "The content must be an array."
                    ],
                    'lesson_ids' => [
                        'The selected lesson ids is invalid.'
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
            ->assertStatus(204);
            
        $this->assertSoftDeleted('lesson_plans', ['id' => $lessonPlan->id]);
    }
}
