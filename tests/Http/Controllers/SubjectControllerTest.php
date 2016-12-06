<?php

namespace Tests\Http\Controllers;

use App\Subject;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class SubjectControllerTest extends TestCase
{
     /**
     * @covers App\Http\Controllers\SubjectController::index
     *
     * @return void
     */
    public function testIndex()
    {
        $subject = factory(Subject::class)->create();
        
        $this->get('api/subjects?_sort=-id',
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($subject->toArray());
    }

    /**
     * @covers App\Http\Controllers\SubjectController::index
     *
     * @return void
     */
    public function testIndexParamQ()
    {
        $name = 'Matematica1';
        $subject = factory(Subject::class)->create([
                'name' => $name
            ])->toArray();

        $struture = [
              "total",
              "per_page",
              "current_page",
              "last_page",
              "next_page_url",
              "prev_page_url",
              "from",
              "to",
              "data" => [
                [
                  "id",
                  "name",
                  "_score"
                ]
              ]
            ];

        $this->get("api/subjects?_q=$name",
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJsonStructure($struture);
    }

    /**
     * @covers App\Http\Controllers\SubjectController::store
     *
     * @return void
     */
    public function testStore()
    {
    	$subject = factory(Subject::class)->make()->toArray();
        
        $this->post('api/subjects',
        	$subject,
        	$this->getAutHeader())
        	->assertResponseStatus(201)
        	->seeJson($subject);
    }

    /**
     * @covers App\Http\Controllers\SubjectController::show
     *
     * @return void
     */
    public function testShow()
    {
    	$subject = factory(Subject::class)->create()->toArray();
        
        $this->get("api/subjects/{$subject['id']}",
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($subject);
    }

    /**
     * @covers App\Http\Controllers\SubjectController::update
     *
     * @return void
     */
    public function testUpdate()
    {
    	$subject = factory(Subject::class)->create();
    	$subject_changed = factory(Subject::class)->make()->toArray();

        $this->put("api/subjects/{$subject->id}",
        	$subject_changed,
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($subject_changed);
    }

    /**
     * @covers App\Http\Controllers\SubjectController::destroy
     * 
     * @return void
     */
    public function testDestroy()
    {
        $subject = factory(Subject::class)->create();

        $this->delete("api/subjects/{$subject->id}",
            [],
            $this->getAutHeader())
            ->assertResponseStatus(204)
            ->seeIsSoftDeletedInDatabase('subjects', ['id' => $subject->id]);
    }
}
