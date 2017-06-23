<?php

namespace Tests\Http\Controllers;

use App\AccountConfig;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AccountConfigControllerTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();

        Artisan::call('db:seed',[
                '--class' => 'AccountConfigSeeder'
            ]);

    }

    /**
     * App\Http\Controllers\AccountConfigController::show
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get('api/account-configs', 
        	$this->getAutHeader())
        ->assertStatus(200)
        ->assertJsonFragmentStructure([
            'account_configs' => [
                '*' => [
                    'id', 'name', 'value', 'default'
                ]
            ]]);

    }


    /**
     * AccountConfigController::update
     *
     * @return void
     */
    public function testUpdate()
    {
        $config = AccountConfig::
            where('name', 'percentage_absences_reprove')
            ->first();
        $config->update(['value' => 20]);


        $this->put("api/account-configs/$config->id",
            ['value' => 30],
            $this->getAutHeader())
        ->assertStatus(200)
        ->assertJsonFragment([
            'account_config' => [
                    'id' => $config->id, 
                    'name' => 'percentage_absences_reprove', 
                    'value' => 30,
                    'default' => "25"
            ]]);

    }
}
