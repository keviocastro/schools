<?php

namespace Tests;

use App\SchoolCalendar;
use App\Student;
use App\Subject;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class StudentTest extends TestCase
{
    /**
     * @covers App\SchoolCalendar::subjectAvaragePerYear
     *
     * @return void
     */
    public function testSubjectAvaragePerYear()
    {
    	Artisan::call('migrate:refresh',[
                '--seed' => true
            ]);

        Artisan::call('db:seed',[
                '--class' => 'SchoolCalendar2016'
            ]);

    	// Criados pelo seeder SchoolCalendar2016
        $student = Student::find(1);
        $schoolCalendar = SchoolCalendar::find(1);
        $subject = Subject::find(1);

        $averages = $student->subjectAvaragePerYear($schoolCalendar);
        $result = $averages->where('id', $subject->id)->first();
        $expected = array_merge($subject->toArray(), [
        		'average_year' => 9.5,
        		'average_calculation' => 
        			'( (9.6 + 9.3)*0.4 + (9.3 + 9.8)*0.6 )/2',
                'average_formula' => '( ({1º Bimestre} + {2º Bimestre})*0.4 + ({3º Bimestre} + {4º Bimestre})*0.6 )/2'
            ]);

        $this->assertEquals(
        	$expected,
        	$result->toArray()
        );
    }
}
