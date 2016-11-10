<?php

namespace Tests;

use App\SchoolClassStudent;
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

        $this->get("api/students/1/annual-summary".
            "?school_calendar_id=1&school_calendar_phase_id=2",
            $this->getAutHeader())->dump()
            ->assertJson([
                "absences_school_calendar_phase" => 4,
                "absences_school_calendar" => 12,
                "best_average_school_calendar_phase" => 9.5,
                "worst_average_school_calendar_phase" => 2.5,
                "best_average_school_calendar" => 9.7,
                "worst_average_school_calendar" => 2.3,
            ]);
        
    } 

}
