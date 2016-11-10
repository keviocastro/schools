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
        	$this->getAutHeader())
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
    	$studantGrade = factory(App\StudentGrades::class)->make()->toArray();
		// Success
        $this->post('api/student-grades',
        	$studantGrade,
        	$this->getAutHeader())
        	->assertResponseStatus(201)
        	->seeJson($studantGrade);

        // grade não pode ser maior que 10
		$studantGrade['grade'] = 20;
		$this->post('api/student-grades',
        	$studantGrade,
        	$this->getAutHeader())
        	->assertResponseStatus(422)
        	->seeJson([
        			'errors' => [
        				'grade' => [
        					"The grade may not be greater than 10."
        				]
        			]
        		]);

        // grade não pode ser menor que 0
        $studantGrade['grade'] = -5;
		$this->post('api/student-grades',
        	$studantGrade,
        	$this->getAutHeader())
        	->assertResponseStatus(422)
        	->seeJsonStructure([
        			'errors' => [
        				'grade'
        			]
        		]);
        
        // O aluno precisa estar na turma 
    	$student = factory(App\Student::class)->create();
    	$schoolClass = factory(App\SchoolClass::class)->create();

    	$studantGrade = factory(App\StudentGrades::class)->make([
    			'student_id' => $student->id,
    			'school_class_id' => $schoolClass->id
    		])->toArray();

        $studantGrade['school_class_id'] = 7923;

    	$this->post('api/student-grades',
        	$studantGrade,
        	$this->getAutHeader())
        	->assertResponseStatus(422);

        //a nota tem que estar relacionada a fase do ano atual do aluno
    	$studantGrade = factory(App\StudentGrades::class)->make()->toArray();

    	$this->post('api/student-grades',
        	$studantGrade,
        	$this->getAutHeader())
        	->assertResponseStatus(201);
        
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
