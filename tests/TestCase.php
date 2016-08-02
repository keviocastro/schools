<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as TestCaseLara;
use Config;

class TestCase extends TestCaseLara
{
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
        $token_id = Config::get('laravel-auth0.token_id_test');
        return ['authorization' => "Bearer $token_id"];
    }
}
