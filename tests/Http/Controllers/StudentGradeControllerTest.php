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
        	->assertStatus(200)
        	->assertJsonFragmentEquals($result);
    }

    /**
     * Test para condições de sucesso e validações
     * 
     * @covers App\Http\Controllers\StudentGradeController::store
     *
     * @return void
     */
    public function testStore()
    {
        /**
         * Success:
         *  - grade é entre 0 e 10
         *  - O aluno está na turma que está sendo registrada a nota
         */
        $schoolCalendarPhase = factory(\App\SchoolCalendarPhase::class)->create();
        $student = factory(\App\Student::class)->create();
        $schoolClass = factory(\App\SchoolClass::class)->create([
            'school_calendar_id' => $schoolCalendarPhase->school_calendar_id
        ]);
        $assessment = factory(\App\Assessment::class)->create([
            'school_calendar_phase_id' => $schoolCalendarPhase->id
        ]);
        factory(\App\SchoolClassStudent::class)->create([
            'student_id' => $student->id,
            'school_class_id' => $schoolClass->id
        ]);
    	$studentGrade = factory(StudentGrade::class)
            ->make([
                'student_id' => $student->id,
                'assessment_id' => $assessment->id,
                'school_class_id' => $schoolClass->id,
            ])
            ->toArray();

        $this->post('api/student-grades',
        	$studentGrade,
        	$this->getAutHeader())
        	->assertStatus(201)
        	->assertJsonFragment($studentGrade);

        // grade não pode ser maior que 10
		$studentGrade['grade'] = 20;
		$this->post('api/student-grades',
        	$studentGrade,
        	$this->getAutHeader())
        	->assertStatus(422)
        	->assertJsonFragment([
        			'errors' => [
        				'grade' => [
        					"The grade may not be greater than 10."
        				]
        			]
        		]);

        // grade não pode ser menor que 0
        $studentGrade['grade'] = -5;
		$this->post('api/student-grades',
        	$studentGrade,
        	$this->getAutHeader())
        	->assertStatus(422)
        	->assertJsonFragmentStructure([
        			'errors' => [
        				'grade'
        			]
        		]);

        //O aluno precisa estar na turma que está sendo registrada a nota
        $schoolClass = factory(SchoolClass::class)->create();
        $studentGrade = factory(StudentGrade::class)->make([
                'school_class_id' => $schoolClass->id
            ])->toArray();
            
        $this->post('api/student-grades',
            $studentGrade,
            $this->getAutHeader())
            ->assertStatus(409)
            ->assertJsonFragment(['message' => 
                "The student is not in the school class ({$studentGrade['school_class_id']})."]);        
    }

    /**
     * Test para criar multiplos registros
     * 
     * @covers App\Http\Controllers\StudentGradeController::store
     *
     * @return void
     */
    public function testStoreMultipleRecords()
    {
        // Criando um calendário com 1 avaliação em uma fase do calendário
        $schoolClass = factory(\App\SchoolClass::class)->create();
        $assessment_id = factory(\App\Assessment::class)->create([
            'school_calendar_phase_id' => function() use ($schoolClass){
                return factory(\App\SchoolCalendarPhase::class)->create([
                    'school_calendar_id' => $schoolClass->schoolCalendar->id
                ])->id;
            }
        ])->id;

        // Criando o aluno e inserindo na turma
        $student = factory(\App\Student::class)->create();
        factory(\App\SchoolClassStudent::class)->create([
            'student_id' => $student->id,
            'school_class_id' => $schoolClass->id
            ]);

        // Gerando dados aleatórios da nota
        $studentGrade = factory(StudentGrade::class, 3)->make([
            'student_id' => $student->id,
            'school_class_id' => $schoolClass->id,
            'assessment_id' => $assessment_id
        ])->toArray();

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
        $studentGrade = factory(StudentGrade::class)->create();
    	
        $this->get("api/student-grades/{$studentGrade->id}",
        	$this->getAutHeader())
        	->assertStatus(200)
        	->assertJsonFragment($studentGrade->toArray());
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
        $studentGrade = factory(StudentGrade::class)->create();
        $studentGrade_changed = $studentGrade->toArray();
        $studentGrade_changed['grade'] = 9.9;

        $this->put("api/student-grades/{$studentGrade->id}",
            ['grade' => 9.9],
            $this->getAutHeader())
            ->assertStatus(200)
            ->assertJsonFragment($studentGrade_changed);

        //somente a nota pode ser alterada

        $studentGrade = factory(StudentGrade::class)->create();

        $studentGrade_changed = $studentGrade->toArray();
        $studentGrade_changed['student_id'] = 2;
        $studentGrade_changed['grade'] = 1.9;

        $this->put("api/student-grades/{$studentGrade->id}",
            $studentGrade_changed,
            $this->getAutHeader())
            ->assertStatus(409)
            ->assertJsonFragment(['message' => 'Only the grade can be changed.']);

        // Permite alterar para nulo
        $this->put("api/student-grades/{$studentGrade->id}",
            ['grade' => null],
            $this->getAutHeader())
            ->assertStatus(200)
            ->assertJsonFragment(
                array_merge($studentGrade->toArray(), ['grade' => null])
                );
    }

    /**
     * @covers App\Http\Controllers\StudentGradeController
     * 
     * @return void
     */
    public function testDestroy()
    {
        $studentGrade = factory(\App\StudentGrade::class)->create();

        $this->delete("api/student-grades/$studentGrade->id",
            [],
            $this->getAutHeader())
        ->assertStatus(204)
        ->seeIsSoftDeletedInDatabase('student_grades', ['id' => $studentGrade->id]);
    }
}
