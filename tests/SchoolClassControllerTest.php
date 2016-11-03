<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class SchoolClassControllerTest extends TestCase
{
    /**
     * @covers SchoolClassController::index
     *
     * @return void
     */
    public function testIndex()
    {
    	$schoolClass = factory(App\SchoolClass::class)->create();
        
        $this->get('api/school-classes?_sort=-id',
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($schoolClass->toArray());
    }

    /**
     * @covers SchoolClassController::store
     *
     * @return void
     */
    public function testStore()
    {
        $schoolCalendar = factory(App\SchoolCalendar::class)->create();
    	$schoolClass = factory(App\SchoolClass::class)->make()->toArray();
        $schoolClass['school_calendar_id'] = $schoolCalendar->id;
        
        $this->post('api/school-classes',
        	$schoolClass,
        	$this->getAutHeader())
        	->assertResponseStatus(201)
        	->seeJson($schoolClass);
    }

    /**
     * @covers SchoolClassController::show
     *
     * @return void
     */
    public function testShow()
    {
    	$shcoolClass = factory(App\SchoolClass::class)->create()->toArray();
        $this->get("api/school-classes/{$shcoolClass['id']}",
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($shcoolClass);
    }

    /**
     * @covers SchoolClassController::update
     *
     * @return void
     */
    public function testUpdate()
    {
    	$shcoolClass = factory(App\SchoolClass::class)->create();
    	$shcoolClass_changed = factory(App\SchoolClass::class)->make()->toArray();

        $this->put("api/school-classes/{$shcoolClass->id}",
        	$shcoolClass_changed,
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($shcoolClass_changed);
    }

    /**
     * @covers SchoolClassController::destroy
     * 
     * @return void
     */
    public function testDestroy()
    {
        $schoolClass = factory(\App\SchoolClass::class)->create();

        $this->delete("api/school-classes/{$schoolClass->id}",
            [],
            $this->getAutHeader())
            ->assertResponseStatus(204)
            ->seeIsSoftDeletedInDatabase('school_classes', ['id' => $schoolClass->id]);
    }
}
