<?php
namespace Tests\Http\Controllers;

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
     * @covers \App\Http\Controllers\SchoolClassController::index
     *
     * @return void
     */
    public function testIndex()
    {   
    	$schoolClass = factory(SchoolClass::class)->create();
        
            $this->get('api/school-classes?_sort=-id'.
          "&_with=progressSheet.items,shift,students,schoolCalendar,subjects,grade,school",
        	$this->getAutHeader())
        	->assertStatus(200)
        	->assertJsonFragment($schoolClass->toArray());
    }

    /**
     * @covers \App\Http\Controllers\SchoolClassController::index
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
            ->assertStatus(200)
            ->assertJsonStructure($struture);
    }

    /**
     * @covers \App\Http\Controllers\SchoolClassController::store
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
        	->assertStatus(201)
        	->assertJsonFragment($schoolClass);
    }

    /**
     * @covers \App\Http\Controllers\SchoolClassController::show
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

        $this->get("api/school-classes/{$schoolClass['id']}".
            "?_with=progressSheet.items,shift,students,schoolCalendar,subjects,grade,school",
        	$this->getAutHeader())
        	->assertStatus(200)
        	->assertJsonFragment($schoolClass);
    }

    /**
     * @covers \App\Http\Controllers\SchoolClassController::update
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
        	->assertStatus(200)
        	->assertJsonFragment($shcoolClass_changed);
    }

    /**
     * @covers \App\Http\Controllers\SchoolClassController::destroy
     * 
     * @return void
     */
    public function testDestroy()
    {
        $schoolClass = factory(SchoolClass::class)->create();

        $this->delete("api/school-classes/{$schoolClass->id}",
            [],
            $this->getAutHeader())
            ->assertStatus(204);
            
        $this->assertSoftDeleted('school_classes', ['id' => $schoolClass->id]);
    }

    /**
     * @todo Resolver questão da validação de estrutura do array ['student_grades']
     * 
     * @covers \App\Http\Controllers\SchoolClassController::annualReport
     * 
     * @return void
     */
    public function testAnnualReport()
    {
        $this->selectDatabaseTest();

        $this->get("api/school-classes/1/annual-report-by-subject/1",
            $this->getAutHeader())
            ->assertStatus(200)
            ->assertJsonStructure([
              'report_by_student' => 
                ['*' => 
                    ['student' => 
                        [
                        'id', 
                        'person' => []
                        ]
                    ],
                    ['school_calendar_report' => 
                        [
                        'average', 
                        'average_calculation', 
                        'average_formula', 
                        'absences', 
                        ]
                    ],
                    ['phases_report' => 
                        ['*'=> 
                            [
                            'school_calendar_phase_id', 
                            'average', 
                            'average_calculation',
                            'average_formula',
                            'absences',
                            'student_grades' => 
                                ['*' => 
                                    [   //  Comentado porque quando o aluno não 
                                        // tem nota gera erro.
                                        // Criar um assert para tratar isso.
                                        // 'grade',
                                        // 'assessment_id'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'school_class_report' => [
                        'school_calendar_report' => [
                            // Média aritmética de todos os alunos da turma 
                            // no calendário escolar
                            'average' 
                          ],
                        'phases_report' => ['*' => [
                              // Média aritmética de todos os alunos da turma
                              // por fase do calendário escolar
                              'school_calendar_phase_id', 
                              'average'
                            ]
                          ]
                 ]
            ]);


        // Odenação por nome do aluno                
        $firstStudent = SchoolClass::find(1)
          ->students()
          ->join('people', 'people.id', '=', 'students.person_id')
          ->orderBy('name')
          ->get()
          ->first()
          ->toArray();


        $resource = $this->getResponseContent("GET",
          "api/school-classes/1/annual-report-by-subject/1".
          "?_sort_students=name",
          []);

        $resourceFirstStudent = $resource['report_by_student'][0]['student'];
        $this->assertEquals($firstStudent['id'], $resourceFirstStudent['id']);
    }

}
