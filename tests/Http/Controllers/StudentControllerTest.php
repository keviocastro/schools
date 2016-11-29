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
    /**
     * @covers StudentControllerTest::index
     *
     * @return void
     */
    public function testIndex()
    {
    	$student = factory(Student::class)->create();

    	$this->get('api/students',$this->getAutHeader())
    		->assertResponseStatus(200);
    }

    /**
     * @covers StudentControllerTest::show
     *
     * @return void
     */
    public function testShow()
    {
        $student = factory(Student::class)->create()->toArray();
        
        $this->get("api/students/{$student['id']}",
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJson($student);
    }

    /**
     * @todo  Implementar teste unitário para para App\Student
     * 
     * @covers StudentController::annualSummary
     *
     * @return void
     */
    public function testAnnualSummary()
    {

        Artisan::call('migrate:refresh',[
                '--seed' => true
            ]);

        Artisan::call('db:seed',[
                '--class' => 'SchoolCalendar2016'
            ]);

        // Pega o primeiro estudante que foi criado pelo seeder SchoolCalendar2016
        $student = Student::
            orderBy('id', 'asc')
            ->first();

         $schoolCalendar = SchoolCalendar::
            orderBy('id', 'asc')
            ->first();

        $average_structure = [
            'id', 'name', 'average_calculation', 'average',
            'student_grades' => ['*' => [
                    'id', 
                    'grade', 
                    'subject_id',
                    'assessment' => ['name', 'id'],
                ]

            ]
        ];
        
        
        $this->get("api/students/$student->id/annual-summary".
            "?school_calendar_id=$schoolCalendar->id",
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJsonStructure([
                "absences" => ['total'],
                "best_average" => $average_structure,
                "low_average" => $average_structure
            ]);
        
    } 

    /**
     * @covers StudentControllerTest::annualReport
     * 
     * @return void
     */
    public function testAnnualReport()
    {
        // Comentado porque demora muito, e esse teste é executado
        // depois do teste acima que executa os mesmos comandos.
        // Se for testar individualmente, esse trecho deve ser
        // descomentado
        // 
        // Artisan::call('migrate:refresh',[
        //         '--seed' => true
        //     ]);

        // Artisan::call('db:seed',[
        //         '--class' => 'SchoolCalendar2016'
        //     ]);
        
        $this->get('api/students/1/annual-report'.
            "?school_calendar_id=1",
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJsonStructure([
                'subjects' => // Disciplinas
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
                    ]
            ]);
    }

}
