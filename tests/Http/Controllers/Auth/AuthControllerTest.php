<?php

namespace Tests\Http\Controllers\Auth;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    /**
     * @covers App\Http\Controllers\Auth\AuthController::requestAccess
     *
     * @return void
     */
    public function testRequestAccess()
    {   
        $this->post('api/auth/request-access', 
            [],
            $this->getAutHeader())
        	->assertResponseStatus(200)
            ->seeJsonStructure(['request_access' => 
                        ['id','status','status', 'user_id'], // 0 pendente
                    ]);
    }

    /**
     * @covers App\Http\Controllers\Auth\AuthController::showUser
     * 
     * @return void
     */
    public function testShowUser()
    {
        // O usuário é identificado pelo token que está no cabeçario da requisição, 
        // variável "authHeader".
        $this->get('api/auth/user',
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJsonStructure(['user' => ['person', 'teacher', 'student']]);
    }
}
