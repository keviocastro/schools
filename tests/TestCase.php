<?php

namespace Tests;

use Kirkbater\Testing\SoftDeletes as SoftDeletes;
use Auth0\SDK\Auth0AuthApi;
use Illuminate\Foundation\Testing\TestCase as TestCaseLara;
use Config;

class TestCase extends TestCaseLara
{
    use SoftDeletes;

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * Obtem id_token de autentificação auth0 para 
     * testes de autentificação 
     * 
     * @return array
     */
    public function getAutHeader()
    {
        $token = Config::get('laravel-auth0.token_user_tester');
        
        if (empty($token)) {
            $tokens = $this->getTokenUserTester();
            $token = $tokens['id_token'];

            $path = base_path('.env');
            file_put_contents($path, str_replace(
                'AUTH0_TOKEN_USER_TESTER='.Config::get('laravel-auth0.token_user_tester'), 
                'AUTH0_TOKEN_USER_TESTER='.$token, 
                file_get_contents($path)
            ));
        }

        return ['authorization' => "Bearer {$token}"];
    }

    /**
     * Obtem o token_id e access_token 
     * do usuário para automatização de testes
     * 
     * 
     * @return array tokens
     */
    public function getTokenUserTester()
    {
        $auth0Api = new Auth0AuthApi(
            Config::get('laravel-auth0.domain'), 
            Config::get('laravel-auth0.client_id'), 
            Config::get('laravel-auth0.client_secret'));
        
        $tokens = $auth0Api->authorize_with_ro(
            Config::get('laravel-auth0.email_user_tester'),
            Config::get('laravel-auth0.pass_user_tester'),
            'openid',
            'Username-Password-Authentication');
        
        return $tokens;
    }
}
