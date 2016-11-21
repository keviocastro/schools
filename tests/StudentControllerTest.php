<?php

namespace Tests;

use App\SchoolClassStudent;
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
    	$student = factory(\App\Student::class)->create();

    	$this->get('api/students',$this->getAutHeader())
    		->assertResponseStatus(200);
    }

    /**
     * @todo  Remove seeJsonEquals e criar teste para o methodo Student::annualSummary
     *        pois é ele que tem a responsabilidade de gerar a info certa.
     *        Nos testes de controladores só são verificados as estruturas.
     * 
     * @covers StudentController::annualSummary
     *
     * @return void
     */
    public function testAnnualSummary()
    {
        // Precisa remover todos os dados da base,
        // porque se tiver mais dados inseridos do que o Sedder SchoolCalendar2016
        // as quantidades não ficarão corretas
       Artisan::call('migrate:refresh',[
               '--seed' => true
           ]);

        Artisan::call('db:seed',[
                '--class' => 'SchoolCalendar2016'
            ]);

        $subject = Subject::find(1);
        $subject2 = Subject::find(2);
       
        $this->get("api/students/1/annual-summary".
            "?school_calendar_id=1&school_calendar_phase_id=1",
            $this->getAutHeader())
            ->seeJsonEquals([
                "absences_year" => 15,
                'absences_year_phase' => 4,
                "best_average_year" => [
                    'average' => 10,
                    'subject' => $subject->toArray()
                ],
                "low_average_year" => [
                    'average' => 0.2,
                    'subject' => $subject2->toArray()
                ],
                "best_average_year_phase" => [
                    'average' => 10,
                    'subject' => $subject->toArray()
                ],
                "low_average_year_phase" => [
                    'average' => 0.2,
                    'subject' => $subject2->toArray()
                ],
            ]);
        
    } 

    /**
     * @covers StudentControllerTest::annualReport
     * 
     * @return void
     */
    public function testAnnualReport()
    {
        Artisan::call('migrate:refresh',[
               '--seed' => true
           ]);

        Artisan::call('db:seed',[
                '--class' => 'SchoolCalendar2016'
            ]);
        
        $this->get('api/students/1/annual-report'.
            "?school_calendar_id=1",
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJsonStructure([
                'averages' => ['*' => 
                    ['id', // Fase do ano
                    'name', 
                    'start', 
                    'end', 
                    'average_calculation', // Formula do calculo de média
                    'subject_average' => ['*' =>[
                        'name',  // Disciplina 
                        'average_calculation', // Calculo da média
                        'average',  // Média do aluno para disciplina no ano
                        'student_grades' => ['*' => [ // Notas do aluno para disciplina
                                'grade',
                                'student_id',
                                'assessment' => ['name'] // Dado
                                ],
                            ] 
                        ]] 
                    ]
                ],
                'absences' => ['*' => [ // Total de faltas do aluno no ano por 
                                        // disciplina e fase
                    'absences', 
                    'school_calendar_phase_id',
                    'subject_id',
                    ] 

                ],
                // Lista de disciplinas cursadas no ano
                'subjects' => ['*' => ['id', 'name']
                ],
                ]);
    }

}
