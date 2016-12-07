<?php

namespace Http\Controllers;

use App\School;
use App\SchoolClass;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class SchoolControllerTest extends TestCase
{
    /**
     * @covers App\Http\Controllers\SchoolController::store
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
     * @todo  As estruturas de retorno devem ser
     * testadas somente com a doc?
     * 
     * @covers App\Http\Controllers\SchoolController::index
     * 
     * @return void
     */
    public function testIndex()
    {   
        // Se a estrutura de retorno está correta
        // e se está retornado as relações
        $school = factory(School::class)->create();
        $attributes = array_keys($school->attributesToArray());
        
        $this->get('api/schools?_with=schoolClasses',
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJsonStructure([
                'total',
                'per_page',
                'current_page',
                'last_page',
                'prev_page_url',
                'next_page_url',
                'from',
                'to',
                'data' => ['*' => $attributes]
            ]);

        // Se fullsearch está funcionado
        // Verifica se o primeiro retornado é o mesmo
        // que foi pesquisado
        $name = 'School sunshine '.str_random(5);
        $school = factory(School::class)->create([
                'name' => $name
            ])->toArray();

        $result = $this->getResponseContent('GET', 
            "api/schools?_q=$name");
        $this->assertEquals($school['id'], $result['data'][0]['id']);
    }

    /**
     * @covers App\Http\Controllers\SchoolController::show
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
     * @covers App\Http\Controllers\SchoolController::destroy
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
     * @covers App\Http\Controllers\SchoolController::update
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
