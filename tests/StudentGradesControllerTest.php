<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StudentGradesControllerTest extends TestCase
{
    /**
     * StudentGradesControllerTest::index
     *
     * @return void
     */
    public function testIndex()
    {
    	$studantGrades = factory(App\StudentGrades::class)->create();

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
			      "grade",
			      "student_id",
			      "subject_id",
			      "assessment_id",
			      "owner_person_id",
			      "created_at",
			      "updated_at",
			    ]
			  ]
			];
        $this->get('api/student-grades?with=student,subject,assessment,schoolClass,ownerPerson',
        	$this->getAutHeader())->dump()
        	->assertResponseStatus(200)
        	->seeJsonStructure($struture);
    }

    /**
     * StudentGradesControllerTest::store
     *
     * @return void
     */
    public function testStore()
    {
    	$studantGrades = factory(App\StudentGrades::class)->make()->toArray();

    	// dd($studantGrades);

        $this->post('api/student-grades',
        	$studantGrades,
        	$this->getAutHeader())
        	->assertResponseStatus(201)
        	->seeJson($studantGrades);
    }

    /**
     * StudentGradesControllerTest::show
     *
     * @return void
     */
    public function testShow()
    {
        $studantGrades = factory(App\StudentGrades::class)->create();
    	
        $this->get("api/student-grades/{$studantGrades->id}",
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($studantGrades->toArray());
    }

    /**
     * StudentGradesControllerTest::update
     *
     * @return void
     */
    public function testUpdate()
    {
        $studantGrades = factory(App\StudentGrades::class)->create();
        $studantGrades_changed = factory(App\StudentGrades::class)->make()->toArray();

        $this->put("api/student-grades/{$studantGrades->id}",
            $studantGrades_changed,
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJson($studantGrades_changed);
    }
}
