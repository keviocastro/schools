<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OccurenceControllerTest extends TestCase
{
    /**
     * A basic test example.
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
			      "comment",
			      "owner_person_id",
			      "about_person_id",
			      "level_id",
			      "created_at",
			      "updated_at",
			      "level" => ['id','name'],
			      "about_person" => [
			      	'id','name','birthday','gender','place_of_birth','more'
			      ],
			      "owner_person" =>[
			      	'id','name','birthday','gender','place_of_birth','more'
			      ]
			    ]
			  ]
			];
        $this->get('api/occurences?with=level,aboutPerson,ownerPerson',
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJsonStructure($struture);
    }
}
