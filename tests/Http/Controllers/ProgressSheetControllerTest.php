<?php

namespace Tests\Http\Controllers;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\ProgressSheet;
use Tests\TestCase;


class ProgressSheetControllerTest extends TestCase
{
     /**
     * @covers App\Http\Controllers\ProgressSheetController::index
     *
     * @return void
     */
    public function testIndex()
    {
        $progressSheet = factory(ProgressSheet::class)->create();
        
        $json = [
                'total',
                'per_page',
                'current_page',
                'last_page',
                'prev_page_url',
                'next_page_url',
                'from',
                'to',
                'data' => ['*' => array_keys($progressSheet->attributesToArray())]
        ];

        $this->get('api/progress-sheets?_sort=-id',
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJsonStructure($json);
    }

    /**
     * @covers App\Http\Controllers\ProgressSheetController::store
     *
     * @return void
     */
    public function testStore()
    {
    	$progressSheet = factory(ProgressSheet::class)->make()->toArray();
        
        $this->post('api/progress-sheets',
        	$progressSheet,
        	$this->getAutHeader())
        	->assertResponseStatus(201)
        	->seeJson($progressSheet);
    }

    /**
     * @covers App\Http\Controllers\ProgressSheetController::show
     *
     * @return void
     */
    public function testShow()
    {
    	$progressSheet = factory(ProgressSheet::class)->create();
        
        $json = [
            "progress_sheet" => 
                array_keys($progressSheet->attributesToArray())
            
        ];

        $this->get("api/progress-sheets/{$progressSheet->id}",
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJsonStructure($json);
    }

    /**
     * @covers App\Http\Controllers\ProgressSheetController::update
     *
     * @return void
     */
    public function testUpdate()
    {
    	$progressSheet = factory(ProgressSheet::class)->create();
    	$progressSheet_changed = factory(ProgressSheet::class)->make()->toArray();

        $this->put("api/progress-sheets/{$progressSheet->id}",
        	$progressSheet_changed,
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($progressSheet_changed);
    }

    /**
     * @covers App\Http\Controllers\ProgressSheetController::destroy
     * 
     * @return void
     */
    public function testDestroy()
    {
        $progressSheet = factory(ProgressSheet::class)->create();

        $this->delete("api/progress-sheets/{$progressSheet->id}",
            [],
            $this->getAutHeader())
            ->assertResponseStatus(204)
            ->seeIsSoftDeletedInDatabase('progress_sheets', ['id' => $progressSheet->id]);
    }
}
