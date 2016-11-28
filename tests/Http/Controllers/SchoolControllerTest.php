<?php

namespace Http\Controllers;

use App\School;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class SchoolControllerTest extends TestCase
{
    /**
     * @covers SchoolController::store
     *
     * @return void
     */
    public function testStore()
    {
    	$school = factory(School::class)->make()->toArray();

        $this->post('api/schools', 
            $school,
            $this->getAutHeader())
        	->assertResponseStatus(201)
        	->seeJson($school);
    }

    /**
     * @covers SchoolController::index
     * 
     * @return void
     */
    public function testIndex()
    {
        $this->get('api/schools?_q=&_sort',
            $this->getAutHeader())
            ->assertResponseStatus(200);
    }

    /**
     * @covers SchoolController::show
     * 
     * @return void
     */
    public function testShow()
    {
        $school = factory(School::class)->create();

        $this->get("api/schools/{$school->id}",
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJson($school->toArray());
    }

    /**
     * @covers SchoolController::destroy
     * 
     * @return void
     */
    public function testDestroy()
    {
        $school = factory(School::class)->create();

        $this->delete("api/schools/{$school->id}",
            [],
            $this->getAutHeader())
            ->assertResponseStatus(204)
            ->seeIsSoftDeletedInDatabase('schools', ['id' => $school->id]);
    }

    /**
     * @covers SchoolController::update
     * 
     * @return void
     */
    public function testUpdate()
    {
        $school = factory(School::class)->create()->toArray();
        $school_changed = factory(School::class)->make()->toArray();
        
        $this->put("api/schools/{$school['id']}",
            $school_changed,
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJson($school_changed);
    }
}
