<?php
namespace Tests\Http\Controllers;

use App\SchoolCalendar;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class SchoolCalendarControllerTest extends TestCase
{
     /**
     * @covers App\Http\Controllers\SchoolCalendarController::index
     *
     * @return void
     */
    public function testIndex()
    {
        $schoolCalendar = factory(SchoolCalendar::class)->create();
        
        $this->get('api/school-calendars?_sort=-id',
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($schoolCalendar->toArray());
    }

    /**
     * @covers App\Http\Controllers\SchoolCalendarController::store
     *
     * @return void
     */
    public function testStore()
    {
    	$schoolCalendar = factory(SchoolCalendar::class)->make()->toArray();
        
        $this->post('api/school-calendars',
        	$schoolCalendar,
        	$this->getAutHeader())
        	->assertResponseStatus(201)
        	->seeJson($schoolCalendar);
    }

    /**
     * @covers App\Http\Controllers\SchoolCalendarController::show
     *
     * @return void
     */
    public function testShow()
    {
    	$schoolCalendar = factory(SchoolCalendar::class)->create()->toArray();
        
        $this->get("api/school-calendars/{$schoolCalendar['id']}",
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($schoolCalendar);
    }

    /**
     * @covers App\Http\Controllers\SchoolCalendarController::update
     *
     * @return void
     */
    public function testUpdate()
    {
    	$schoolCalendar = factory(SchoolCalendar::class)->create();
    	$schoolCalendar_changed = factory(SchoolCalendar::class)->make()->toArray();

        $this->put("api/school-calendars/{$schoolCalendar->id}",
        	$schoolCalendar_changed,
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($schoolCalendar_changed);
    }

    /**
     * @covers App\Http\Controllers\SchoolCalendarController::destroy
     * 
     * @return void
     */
    public function testDestroy()
    {
        $schoolCalendar = factory(SchoolCalendar::class)->create();

        $this->delete("api/school-calendars/{$schoolCalendar->id}",
            [],
            $this->getAutHeader())
            ->assertResponseStatus(204)
            ->seeIsSoftDeletedInDatabase('school_calendars', ['id' => $schoolCalendar->id]);
    }
}
