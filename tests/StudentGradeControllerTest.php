<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StudentGradeControllerTest extends TestCase
{
    /*
     * StudentGradeControllerTest::index
     *
     * @return void
     */
    public function testIndex()
    {
    	$studantGrade = factory(App\StudentGrade::class)->create();

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
			    ]
			  ]
			];
        $this->get('api/student-grades?with=student,subject,assessment,schoolClass',
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJsonStructure($struture);
    }

    /**
     * StudentGradeControllerTest::store
     *
     * @return void
     */
    public function testStore()
    {
		// Success
    	$studantGrade = factory(App\StudentGrade::class)->make()->toArray();

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

    	$studantGrade = factory(App\StudentGrade::class)->make([
    			'student_id' => $student->id,
    			'school_class_id' => $schoolClass->id
    		])->toArray();

    	$schoolClass = factory(App\SchoolClass::class)->create();
        $studantGrade['school_class_id'] = $schoolClass->id;

    	$this->post('api/student-grades',
        	$studantGrade,
        	$this->getAutHeader())
        	->assertResponseStatus(422);

        //a nota tem que estar relacionada a fase do ano atual do aluno
    	$studantGrade = factory(App\StudentGrade::class)->make()->toArray();

    	$this->post('api/student-grades',
        	$studantGrade,
        	$this->getAutHeader())
        	->assertResponseStatus(201);

        //Cadastrar multiplos registros.
        $studentGrade = factory(App\StudentGrade::class, 3)->make()->toArray();

        $autHeader = $this->transformHeadersToServerVars($this->getAutHeader());

        $response = $this->call('POST',
            'api/student-grades',
            $studentGrade,
            [],
            [],
            $autHeader);

        $responseData = collect(json_decode($response->getContent(), true)['student_grades']);


        foreach ($studentGrade as $key => $grade) {
            
            $actual = $responseData->filter(function($item, $key) use ($grade){
                return $item['grade'] == $grade['grade'] &&
                    $item['student_id'] == $grade['student_id'] &&
                    $item['subject_id'] == $grade['subject_id'] &&
                    $item['assessment_id'] == $grade['assessment_id'] &&
                    $item['school_class_id'] == $grade['school_class_id'];
            });      

            $actual = $actual->first();
            unset($actual['id']);
            $this->assertEquals($grade, $actual);
            
        }

        $this->assertEquals(201, $response->getStatusCode());
        
    }

    /**
     * StudentGradeControllerTest::show
     *
     * @return void
     */
    public function testShow()
    {
        $studantGrades = factory(App\StudentGrade::class)->create();
    	
        $this->get("api/student-grades/{$studantGrades->id}",
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($studantGrades->toArray());
    }

    /**
     * @todo A nota somente podera ser edita caso nao esteja concluída a fase do ano pelo professor  
     * 
     * StudentGradeControllerTest::update
     *
     * @return void
     */
    public function testUpdate()
    {
        $studantGrades = factory(App\StudentGrade::class)->create();
        $studantGrade_changed = $studantGrades->toArray();
        $studantGrade_changed['grade'] = '9.9';

        $this->put("api/student-grades/{$studantGrades->id}",
            $studantGrade_changed,
            $this->getAutHeader())
            ->assertResponseStatus(200);
            // ->seeJson($studantGrade_changed);

        //somente a nota pode ser alterada
        
        $studantGrades = factory(App\StudentGrade::class)->create();

        $studantGrade_changed = $studantGrades->toArray();
        $studantGrade_changed['student_id'] = 2;
        $studantGrade_changed['grade'] = 1.9;

        $this->put("api/student-grades/{$studantGrades->id}",
            $studantGrade_changed,
            $this->getAutHeader())
            ->assertResponseStatus(422);
    }
}
