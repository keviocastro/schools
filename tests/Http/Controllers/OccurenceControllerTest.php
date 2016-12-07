<?php
namespace Tests\Http\Controllers;

use App\Occurence;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class OccurenceControllerTest extends TestCase
{
    /**
     * @covers App\Http\Controllers\OccurenceController::index
     *
     * @return void
     */
    public function testIndex()
    {
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
                  "about_person_id",
                  'created_at',
                  'updated_at',
                  "about_person" => ['id','name'],
                  "level" => ['id','name']
                ]
              ]
            ];
            
        $this->get('api/occurences?_with=level,aboutPerson',
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJsonStructure($struture);
    }
    /**
     * @covers App\Http\Controllers\OccurenceController::index
     *
     * @return void
     */
    public function testIndexParamQ()
    {
        $comment = 'Apos o recreio, saiu da sala correndo';
        $schoolClass = factory(Occurence::class)->create([
                'comment' => $comment
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
                  "level_id",
                  "comment",
                  "about_person_id",
                  "_score"
                ]
              ]
            ];
        $word = explode(' ', $comment, 4)[2];
        $this->get("api/occurences?_q=$word",
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJsonStructure($struture);
    }

    /**
     * @covers App\Http\Controllers\OccurenceController::store
     *
     * @return void
     */
    public function testStore()
    {
    	$ocurrence = factory(Occurence::class)->make()->toArray();

        $this->post('api/occurences',
        	$ocurrence,
        	$this->getAutHeader())
        	->assertResponseStatus(201)
        	->seeJson($ocurrence);
    }


    /**
     * @covers App\Http\Controllers\OccurenceController::show
     *
     * @return void
     */
    public function testShow()
    {
    	$occurence = factory(Occurence::class)->create();
    	
        $this->get("api/occurences/{$occurence->id}",
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($occurence->toArray());
    }

    /**
     * @covers App\Http\Controllers\OccurenceController::destroy
     *
     * @return void
     */
    public function testDestroy()
    {
        $occurence = factory(Occurence::class)->create();

        $this->delete("api/occurences/{$occurence->id}",
            [],
            $this->getAutHeader())
            ->assertResponseStatus(204)
            ->seeIsSoftDeletedInDatabase('occurences', ['id' => $occurence->id]);
    }

    /**
     * @covers App\Http\Controllers\OccurenceController::update
     *
     * @return void
     */
    public function testUpdate()
    {
        $occurence = factory(Occurence::class)->create();
        $occurence_changed = factory(Occurence::class)->make()->toArray();

        $this->put("api/occurences/{$occurence->id}",
            $occurence_changed,
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJson($occurence_changed);
    }
}
