<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LessonPlanModelControllerTest extends TestCase
{
     /**
     * @covers App\Http\Controllers\LessonPlanModelController::index
     *
     * @return void
     */
    public function testIndex()
    {
        $lessonPlanModel = factory(App\LessonPlanModel::class)->create();

        $this->get('api/lesson-plan-models?_sort=-id',
        	$this->getAutHeader())
        	->assertStatus(200)
        	->assertJsonFragment($lessonPlanModel->toArray());
    }

    /**
     * @covers App\Http\Controllers\LessonPlanModelController::store
     *
     * @return void
     */
    public function testStore()
    {
    	$lessonPlanModel = factory(App\LessonPlanModel::class)->make()->toArray();
        
        $this->post('api/lesson-plan-models',
        	$lessonPlanModel,
        	$this->getAutHeader())
        	->assertStatus(201)
        	->assertJsonFragment($lessonPlanModel);
    }

    /**
     * @covers App\Http\Controllers\LessonPlanModelController::store
     *
     * @return void
     */
    public function testStoreValidation()
    {
        //Testando a validacao do componente, e do campo content
        $lessonPlanModel = factory(App\LessonPlanModel::class)->make()->toArray();
        
        $lessonPlanModel['definition'] = 'Testando o com string';

        $this->post('api/lesson-plan-models',
            $lessonPlanModel,
            $this->getAutHeader())
            ->assertStatus(422)
            ->assertJsonFragment([
                    'errors' => [
                        'definition' => [
                            "The definition must be an array."
                        ]
                    ]
                ]);
    }

    /**
     * @covers App\Http\Controllers\LessonPlanModelController::show
     *
     * @return void
     */
    public function testShow()
    {
    	$lessonPlanModel = factory(App\LessonPlanModel::class)->create()->toArray();
        
        $this->get("api/lesson-plan-models/{$lessonPlanModel['id']}",
        	$this->getAutHeader())
        	->assertStatus(200)
        	->assertJsonFragment($lessonPlanModel);
    }

    /**
     * @covers App\Http\Controllers\LessonPlanModelController::update
     *
     * @return void
     */
    public function testUpdate()
    {
    	$lessonPlanModel = factory(App\LessonPlanModel::class)->create();
    	$lessonPlanModel_changed = factory(App\LessonPlanModel::class)->make()->toArray();

        $this->put("api/lesson-plan-models/{$lessonPlanModel->id}",
        	$lessonPlanModel_changed,
        	$this->getAutHeader())
        	->assertStatus(200)
        	->assertJsonFragment($lessonPlanModel_changed);
    }

    /**
     * @covers App\Http\Controllers\LessonPlanModelController::destroy
     * 
     * @return void
     */
    public function testDestroy()
    {
        $lessonPlanModel = factory(\App\LessonPlanModel::class)->create();

        $this->delete("api/lesson-plan-models/{$lessonPlanModel->id}",
            [],
            $this->getAutHeader())
            ->assertStatus(204);
            
        $this->assertSoftDeleted('lesson_plan_models', ['id' => $lessonPlanModel->id]);
    }
}
