<?php

namespace Http\Controllers;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AccountConfigControllerTest extends TestCase
{
    /**
     * AccountConfigController::show
     *
     * @return void
     */
    public function testShow()
    {
        $this->get('api/account-config', 
        	$this->getAutHeader())
        ->assertResponseStatus(200);
        // ->seeJsonStructure(['account_config' => [
        // 		'percentage_absences_reprove' => ['value'],
        // 		'grade_threshold_great' => ['value'],
        // 		'grade_threshold_good' => ['value'],
        // 		'passing_grade_threshold' => ['value'],
        // 	]]);
    }
}
