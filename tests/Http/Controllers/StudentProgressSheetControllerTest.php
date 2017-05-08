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

    // /**
    //  * @covers App\Http\Controllers\StudentProgressSheetController::store
    //  *
    //  * @return void
    //  */
    // public function testStore()
    // {
    // 	$studentProgressSheet = factory(App\StudentProgressSheet::class)->make()->toArray();
        
    //     $this->post('api/student-progress-sheets',
    //     	$studentProgressSheet,
    //     	$this->getAutHeader())
    //     	->assertResponseStatus(201)
    //     	->seeJson($studentProgressSheet);
    // }

    // /**
    //  * @covers App\Http\Controllers\StudentProgressSheetController::show
    //  *
    //  * @return void
    //  */
    // public function testShow()
    // {
    // 	$studentProgressSheet = factory(App\StudentProgressSheet::class)->create()->toArray();
        
    //     $this->get("api/student-progress-sheets/{$studentProgressSheet['id']}",
    //     	$this->getAutHeader())
    //     	->assertResponseStatus(200)
    //     	->seeJson($studentProgressSheet);
    // }

    // /**
    //  * @covers App\Http\Controllers\StudentProgressSheetController::update
    //  *
    //  * @return void
    //  */
    // public function testUpdate()
    // {
    // 	$studentProgressSheet = factory(App\StudentProgressSheet::class)->create();
    // 	$studentProgressSheet_changed = factory(App\StudentProgressSheet::class)->make()->toArray();

    //     $this->put("api/student-progress-sheets/{$studentProgressSheet->id}",
    //     	$studentProgressSheet_changed,
    //     	$this->getAutHeader())
    //     	->assertResponseStatus(200)
    //     	->seeJson($studentProgressSheet_changed);
    // }

    // /**
    //  * @covers App\Http\Controllers\StudentProgressSheetController::destroy
    //  * 
    //  * @return void
    //  */
    // public function testDestroy()
    // {
    //     $studentProgressSheet = factory(\App\StudentProgressSheet::class)->create();

    //     $this->delete("api/student-progress-sheets/{$studentProgressSheet->id}",
    //         [],
    //         $this->getAutHeader())
    //         ->assertResponseStatus(204)
    //         ->seeIsSoftDeletedInDatabase('student_progress_sheets', ['id' => $studentProgressSheet->id]);
    // }
}
