<?php
namespace Http\Controllers;

use App\SchoolCalendar;
use App\SchoolClass;
use App\SchoolClassStudent;
use App\Student;
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
    	$schoolClass = factory(SchoolClass::class)->create();
        
        $this->get('api/school-classes?_sort=-id',
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($schoolClass->toArray());
    }

    /**
     * @covers SchoolClassController::index
     *
     * Teste do parametro _q = Full text search
     * 
     * @return void
     */
    public function testIndexParamQ()
    {

        //Testando a chave de busca _q
        // Verifica se o primeiro retornado é o mesmo
        // que foi pesquisado
        $identifier = 'fff';
        $schoolClass = factory(SchoolClass::class)->create([
                'identifier' => $identifier
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
                    "identifier",
                    "shift_id",
                    "grade_id",
                    "school_id",
                    "school_calendar_id",
                    "_score"
                ]
              ]
            ];

        $this->get("api/school-classes?_q=$identifier",
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJsonStructure($struture);
    }

    /**
     * @covers SchoolClassController::store
     *
     * @return void
     */
    public function testStore()
    {
        $schoolCalendar = factory(SchoolCalendar::class)->create();
    	$schoolClass = factory(SchoolClass::class)->make()->toArray();
        $schoolClass['school_calendar_id'] = $schoolCalendar->id;
        
        $this->post('api/school-classes',
        	$schoolClass,
        	$this->getAutHeader())
        	->assertResponseStatus(201)
        	->seeJson($schoolClass);
    }

    /**
     * @covers SchoolClassController::show
     *
     * @return void
     */
    public function testShow()
    {
    	$schoolClass = factory(SchoolClass::class)->create()->toArray();
        $students = factory(Student::class, 3)->create();
        $students->each(function($item, $key) use ($schoolClass){
            factory(SchoolClassStudent::class)->create([
                    'student_id' => $item->id,
                    'school_class_id' => $schoolClass['id']
                ]);
        });

        $this->get("api/school-classes/{$schoolClass['id']}",
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($schoolClass);
    }

    /**
     * @covers SchoolClassController::update
     *
     * @return void
     */
    public function testUpdate()
    {
    	$shcoolClass = factory(SchoolClass::class)->create();
    	$shcoolClass_changed = factory(SchoolClass::class)->make()->toArray();

        $this->put("api/school-classes/{$shcoolClass->id}",
        	$shcoolClass_changed,
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($shcoolClass_changed);
    }

    /**
     * @covers SchoolClassController::destroy
     * 
     * @return void
     */
    public function testDestroy()
    {
        $schoolClass = factory(SchoolClass::class)->create();

        $this->delete("api/school-classes/{$schoolClass->id}",
            [],
            $this->getAutHeader())
            ->assertResponseStatus(204)
            ->seeIsSoftDeletedInDatabase('school_classes', ['id' => $schoolClass->id]);
    }
}
