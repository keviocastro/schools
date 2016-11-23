<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SchoolCalendarControllerTest extends TestCase
{
     /**
     * @covers SchoolCalendarControllerTest::index
     *
     * @return void
     */
    public function testIndex()
    {
        $schoolCalendar = factory(App\SchoolCalendar::class)->create();
        
        $this->get('api/school-calendars?_sort=-id',
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($schoolCalendar->toArray());
    }

    /**
     * @covers SchoolCalendarControllerTest::store
     *
     * @return void
     */
    public function testStore()
    {
    	$schoolCalendar = factory(App\SchoolCalendar::class)->make()->toArray();
        
        $this->post('api/school-calendars',
        	$schoolCalendar,
        	$this->getAutHeader())
        	->assertResponseStatus(201)
        	->seeJson($schoolCalendar);
    }

    /**
     * @covers SchoolCalendarControllerTest::show
     *
     * @return void
     */
    public function testShow()
    {
    	$schoolCalendar = factory(App\SchoolCalendar::class)->create()->toArray();
        
        $this->get("api/school-calendars/{$schoolCalendar['id']}",
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($schoolCalendar);
    }

    /**
     * @covers SchoolCalendarControllerTest::update
     *
     * @return void
     */
    public function testUpdate()
    {
    	$schoolCalendar = factory(App\SchoolCalendar::class)->create();
    	$schoolCalendar_changed = factory(App\SchoolCalendar::class)->make()->toArray();

        $this->put("api/school-calendars/{$schoolCalendar->id}",
        	$schoolCalendar_changed,
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($schoolCalendar_changed);
    }

    /**
     * @covers SchoolCalendarControllerTest::destroy
     * 
     * @return void
     */
    public function testDestroy()
    {
        $schoolCalendar = factory(\App\SchoolCalendar::class)->create();

        $this->delete("api/school-calendars/{$schoolCalendar->id}",
            [],
            $this->getAutHeader())
            ->assertResponseStatus(204)
            ->seeIsSoftDeletedInDatabase('school_calendars', ['id' => $schoolCalendar->id]);
    }
}
