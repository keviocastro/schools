<?php

namespace Tests\Http\Controllers\Auth;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use App\Person;
use App\Teacher;

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
        $person_id = config('laravel-auth0.user_id_role_teacher_1');
        $person = Person::firstOrCreate(['user_id' => $person_id]);

        $teacher = Teacher::firstOrCreate(['person_id' => $person->id]);


        // O usuário é identificado pelo token que está no cabeçario da requisição, 
        // variável "authHeader".
        $this->get('api/auth/user',
            $this->getAutHeader())->dump()
            ->assertResponseStatus(200)
            ->seeJson($teacher->toArray());
    }
}
