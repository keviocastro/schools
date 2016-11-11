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
        // as quantidades não ficarao corretas
       Artisan::call('migrate:refresh',[
               '--seed' => true
           ]);

        Artisan::call('db:seed',[
                '--class' => 'SchoolCalendar2016'
            ]);
       
       $subject_attributes = array_keys(factory(Subject::class)->make()->toArray());

        $this->get("api/students/1/annual-summary".
            "?school_calendar_id=1&school_calendar_phase_id=1",
            $this->getAutHeader())
            ->seeJsonStructure([
                "absences_year",
                'absences_year_phase',
                "best_average_year" => [
                    'average', 
                    'subject' => $subject_attributes
                ],
                "low_average_year" => [
                    'average', 
                    'subject' => $subject_attributes
                ],
                "best_average_year_phase" => [
                    'average', 
                    'subject' => $subject_attributes
                ],
                "low_average_year_phase" => [
                    'average', 
                    'subject' => $subject_attributes
                ],
            ])
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
            ])
            ;
        
    } 

}
