<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StudentProgressSheetControllerTest extends TestCase
{
     /**
     * @covers App\Http\Controllers\StudentProgressSheetController::index
     *
     * @return void
     */
    public function testIndex()
    {
        $studentProgressSheet = factory(App\StudentProgressSheet::class)->create();
        
        $this->get('api/student-progress-sheets?_sort=-id',
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($studentProgressSheet->toArray());
    }
}
