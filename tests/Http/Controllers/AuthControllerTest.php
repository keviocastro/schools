<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
	// use WithoutMiddleware;

    /**
     * @covers App\Http\Controllers\Auth\AuthController::requestAccess
     *
     * @return void
     */
    public function testRequestAccessSuccess()
    {   
        $this->get('api/auth/request-access',
            $this->getAutHeader())->dump()
        	->assertResponseStatus(200);
    }
}