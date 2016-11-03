<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OccurenceControllerTest extends TestCase
{
    /**
     * OccurenceControllerTest::index
     *
     * @return void
     */
    public function testIndex()
    {
    	$ocurrence = factory(App\Occurence::class)->create();

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
			      "level_id",
			      "comment",
			      "owner_person_id",
			      "about_person_id",
			      "created_at",
			      "updated_at",
			      "level" => ['id','name']
			    ]
			  ]
			];
        $this->get('api/occurences?_with=level',
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJsonStructure($struture);
    }

    /**
     * OccurenceControllerTest::store
     *
     * @return void
     */
    public function testStore()
    {
    	$ocurrence = factory(App\Occurence::class)->make()->toArray();

    	// dd($ocurrence);

        $this->post('api/occurences',
        	$ocurrence,
        	$this->getAutHeader())
        	->assertResponseStatus(201)
        	->seeJson($ocurrence);
    }


    /**
     * OccurenceControllerTest::show
     *
     * @return void
     */
    public function testShow()
    {
    	$occurence = factory(App\Occurence::class)->create();
    	
        $this->get("api/occurences/{$occurence->id}",
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($occurence->toArray());
    }

    /**
     * OccurenceControllerTest::destroy
     *
     * @return void
     */
    public function testDestroy()
    {
        $occurence = factory(\App\Occurence::class)->create();

        $this->delete("api/occurences/{$occurence->id}",
            [],
            $this->getAutHeader())
            ->assertResponseStatus(204)
            ->seeIsSoftDeletedInDatabase('occurences', ['id' => $occurence->id]);
    }

    /**
     * OccurenceControllerTest::update
     *
     * @return void
     */
    public function testUpdate()
    {
        $occurence = factory(App\Occurence::class)->create();
        $occurence_changed = factory(App\Occurence::class)->make()->toArray();

        $this->put("api/occurences/{$occurence->id}",
            $occurence_changed,
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJson($occurence_changed);
    }
}
