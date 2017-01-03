<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LessonPlanControllerTest extends TestCase
{
     /**
     * @covers LessonPlanControllerTest::index
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
     * @covers LessonPlanControllerTest::store
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
     * @covers LessonPlanControllerTest::show
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
     * @covers LessonPlanControllerTest::update
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
     * @covers LessonPlanControllerTest::destroy
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
