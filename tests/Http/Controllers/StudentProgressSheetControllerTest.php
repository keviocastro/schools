<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\StudentProgressSheet;

class StudentProgressSheetControllerTest extends TestCase
{
     /**
     * @covers App\Http\Controllers\StudentProgressSheetController::index
     *
     * @return void
     */
    public function testIndex()
    {
        $studentProgressSheet = factory(StudentProgressSheet::class)->create();
        
        $this->get('api/student-progress-sheets?_sort=-id',
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($studentProgressSheet->toArray());
    }

    /**
     * @covers App\Http\Controllers\StudentProgressSheetController::store
     *
     * @return void
     */
    public function testStore()
    {
    	$studentProgressSheet = factory(StudentProgressSheet::class)->make()->toArray();

        $this->post('api/student-progress-sheets',
        	$studentProgressSheet,
        	$this->getAutHeader())
        	->assertResponseStatus(201)
        	->seeJson($studentProgressSheet);
    }

    /**
     * @covers App\Http\Controllers\StudentProgressSheetController::show
     *
     * @return void
     */
    public function testShow()
    {
        $studentProgressSheet = factory(StudentProgressSheet::class)->create();

        $structure = [
            'student_progress_sheet' => $studentProgressSheet->toArray()
        ];

        $this->get("api/student-progress-sheets/{$studentProgressSheet->id}",
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJsonEquals($structure);
    }

}
