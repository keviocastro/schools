<?php

namespace Http\Controllers;

use App\SchoolCalendar;
use App\SchoolClassStudent;
use App\Student;
use App\Subject;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class StudentControllerTest extends TestCase
{
    public function setUp()
    {
        $this->average_structure = [
            'id', 'name', 'average_calculation', 'average',
            'student_grades' => ['*' => [
                    'id', 
                    'grade', 
                    'subject_id',
                    'assessment' => ['name', 'id'],
                ]

            ]
        ];

        parent::setUp();
    }

    /**
     * @covers App\Http\Controllers\StudentController::index
     *
     * @return void
     */
    public function testIndex()
    {
    	$student = factory(Student::class)->create();

    	$this->get('api/students',$this->getAutHeader())
    		->assertStatus(200);
    }

    /**
     * @covers App\Http\Controllers\StudentController::show
     *
     * @return void
     */
    public function testShow()
    {
        $student = factory(Student::class)->create()->toArray();
        
        $this->get("api/students/{$student['id']}",
            $this->getAutHeader())
            ->assertStatus(200)
            ->assertJsonFragment($student);
    }

    /**
     * @todo  Implementar teste unitário para para App\Student
     * 
     * @covers App\Http\Controllers\StudentController::annualSummary
     *
     * @return void
     */
    public function testAnnualSummary()
    {
        // O student = 1 e schoolCalendar = 1 
        // são gerados pelo seeder SchoolCalendar2016
        $this->selectDatabaseTest();

        $this->get("api/students/1/annual-summary".
            "?school_calendar_id=1",
            $this->getAutHeader())
            ->assertStatus(200)
            ->assertJsonStructure([
                "absences" => ['total'],
                "best_average" => $this->average_structure,
                "low_average" => $this->average_structure
            ]);
        
    } 

    /**
     * @covers App\Http\Controllers\StudentController::annualReport
     * 
     * @return void
     */
    public function testAnnualReport()
    {
        // O student = 1 e schoolCalendar = 1 
        // são gerados pelo seeder SchoolCalendar2016
        $this->selectDatabaseTest();
        
        $this->get('api/students/1/annual-report'.
            "?school_calendar_id=1",
            $this->getAutHeader())
            ->assertStatus(200)
            ->assertJsonStructure([
                'report_by_subjects' => // Informações por disciplina no ano letivo
                             // faltas, notas e médias
                ['*' => 
                    [
                        'id',  
                        'name',
                        'average_calculation',  // Calculo da média do aluno no ano para disciplina.
                        'average_formula', // Formula utilizada para calcular a média.
                        'average_year', // Ḿédia anual do aluno para disciplina.
                        'school_calendar_phases' => ['*' => // Notas por fase do ano, da disciplina.
                            [
                                'id', 
                                'average', // Média do aluno na fase do ano.
                                'average_formula', // Formula do calculo de média.
                                'average_calculation', // Cálculo da média.
                                'student_grades' => ['*' => // Notas que compêm a média do aluno
                                                            // na fase do ano.
                                    [
                                        'id', 
                                        'grade',
                                        'assessment_id'
                                    ]
                                ]
                            ]
                        ], 
                    ] 
                ],
                'report_by_phases' => // Informações agrupadas por fase do calendário escolar
                ['*' => 
                    [
                        'id',
                        'name',
                        'school_calendar_id',
                        'start',
                        'end',
                        'average_formula',
                        'absences'
                    ]    
                ]
            ]);
    }

}
