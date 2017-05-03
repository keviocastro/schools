<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EvaluationSheetControllerTest extends TestCase
{
     /**
     * @covers App\Http\Controllers\EvaluationSheetController::index
     *
     * @return void
     */
    public function testIndex()
    {
        $evaluationSheet = factory(App\EvaluationSheet::class)->create();
        
        $this->get('api/evaluation-sheets?_sort=-id',
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($evaluationSheet->toArray());
    }

    /**
     * @covers App\Http\Controllers\EvaluationSheetController::store
     *
     * @return void
     */
    public function testStore()
    {
    	$evaluationSheet = factory(App\EvaluationSheet::class)->make()->toArray();
        
        $this->post('api/evaluation-sheets',
        	$evaluationSheet,
        	$this->getAutHeader())
        	->assertResponseStatus(201)
        	->seeJson($evaluationSheet);
    }

    /**
     * @covers App\Http\Controllers\EvaluationSheetController::show
     *
     * @return void
     */
    public function testShow()
    {
    	$evaluationSheet = factory(App\EvaluationSheet::class)->create()->toArray();
        
        $this->get("api/evaluation-sheets/{$evaluationSheet['id']}",
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($evaluationSheet);
    }

    /**
     * @covers App\Http\Controllers\EvaluationSheetController::update
     *
     * @return void
     */
    public function testUpdate()
    {
    	$evaluationSheet = factory(App\EvaluationSheet::class)->create();
    	$evaluationSheet_changed = factory(App\EvaluationSheet::class)->make()->toArray();

        $this->put("api/evaluation-sheets/{$evaluationSheet->id}",
        	$evaluationSheet_changed,
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($evaluationSheet_changed);
    }

    /**
     * @covers App\Http\Controllers\EvaluationSheetController::destroy
     * 
     * @return void
     */
    public function testDestroy()
    {
        $evaluationSheet = factory(\App\EvaluationSheet::class)->create();

        $this->delete("api/evaluation-sheets/{$evaluationSheet->id}",
            [],
            $this->getAutHeader())
            ->assertResponseStatus(204)
            ->seeIsSoftDeletedInDatabase('evaluation_sheets', ['id' => $evaluationSheet->id]);
    }
}
