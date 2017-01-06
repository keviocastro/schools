<?php
namespace Http\Controllers;

use App\SchoolClass;
use App\Student;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\StudentGrade;
use Tests\TestCase;

class StudentGradeControllerTest extends TestCase
{
    /*
     * @covers App\Http\Controllers\StudentGradeController::index
     *
     * @return void
     */
    public function testIndex()
    {
        $studentGrades = factory(StudentGrade::class, 3)->create();
        $ids = $studentGrades->implode('id', '|');
        $studentGrades->load('student', 'subject', 'assessment', 'schoolClass');
    	
        $result = [
			  "total" => 3,
			  "per_page" => 15,
			  "current_page" => 1,
			  "last_page" => 1,
			  "next_page_url" => null,
			  "prev_page_url" => null,
			  "from" => 1,
			  "to" => 3,
			  "data" => $studentGrades->toArray()
			];

        $this->get('api/student-grades?'.
            '_with=student,subject,assessment,schoolClass'.
            "&id=$ids",
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJsonEquals($result);
    }

    /**
     * @covers App\Http\Controllers\StudentGradeController::store
     *
     * @return void
     */
    public function testStore()
    {
		// Success
    	$studantGrade = factory(StudentGrade::class)->make()->toArray();

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

        //O aluno precisa estar na turma que está sendo registrada a nota
        $student = factory(Student::class)->create();
        $schoolClass = factory(SchoolClass::class)->create();

        $studantGrade = factory(StudentGrade::class)->make([
                'student_id' => $student->id,
                'school_class_id' => $schoolClass->id
            ])->toArray();

        $schoolClass = factory(SchoolClass::class)->create();
        $studantGrade['school_class_id'] = $schoolClass->id;

        $this->post('api/student-grades',
            $studantGrade,
            $this->getAutHeader())
            ->assertResponseStatus(409)
            ->seeJson(['message' => 
                "The student is not in the school class ({$studantGrade['school_class_id']})."]);

        //Cadastrar multiplos registros.
        $studentGrade = factory(StudentGrade::class, 3)->make()->toArray();

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
     * @covers App\Http\Controllers\StudentGradeController::show
     *
     * @return void
     */
    public function testShow()
    {
        $studantGrade = factory(StudentGrade::class)->create();
    	
        $this->get("api/student-grades/{$studantGrade->id}",
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($studantGrade->toArray());
    }

    /**
     * @todo A nota somente podera ser edita caso nao esteja concluída a fase do ano pelo professor  
     * 
     * @covers App\Http\Controllers\StudentGradeController::update
     *
     * @return void
     */
    public function testUpdate()
    {
        $studantGrade = factory(StudentGrade::class)->create();
        $studantGrade_changed = $studantGrade->toArray();
        $studantGrade_changed['grade'] = 9.9;

        $this->put("api/student-grades/{$studantGrade->id}",
            ['grade' => 9.9],
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJson($studantGrade_changed);

        //somente a nota pode ser alterada
        $studantGrade = factory(StudentGrade::class)->create();

        $studantGrade_changed = $studantGrade->toArray();
        $studantGrade_changed['student_id'] = 2;
        $studantGrade_changed['grade'] = 1.9;

        $this->put("api/student-grades/{$studantGrade->id}",
            $studantGrade_changed,
            $this->getAutHeader())
            ->assertResponseStatus(409)
            ->seeJson(['message' => 'Only the grade can be changed.']);
    }
}
