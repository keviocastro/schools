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
    	$shcoolClass = factory(App\SchoolClass::class)->create();
        $this->get('api/school-classes',
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($shcoolClass->toArray());
    }

    /**
     * @covers SchoolClassController::store
     *
     * @return void
     */
    public function testStore()
    {
    	$shcoolClass = factory(App\SchoolClass::class)->make()->toArray();
        
        $this->post('api/school-classes',
        	$shcoolClass,
        	$this->getAutHeader())
        	->assertResponseStatus(201)
        	->seeJson($shcoolClass);
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
}
