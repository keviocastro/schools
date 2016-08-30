<?php

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
    	$school = factory(App\School::class)->make()->toArray();

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
        $this->get('api/schools',
            $this->getAutHeader())
            ->assertResponseStatus(200);
    }

    public function testShow()
    {
        $school = factory(App\School::class)->create();

        $this->get("api/schools/{$school->id}",
            $this->getAutHeader())->dump()
            ->assertResponseStatus(200)
            ->seeJson($school->toArray());
    }
}
