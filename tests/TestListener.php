<?php 

namespace Tests;

use Config;
use Illuminate\Support\Facades\Artisan;
use PHPUnit_Framework_BaseTestListener;
use PHPUnit_Framework_TestSuite;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class TestListener extends PHPUnit_Framework_BaseTestListener
{
    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
    }

    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
    }

    public function destroyAuthToken()
    {
        file_put_contents($path, str_replace(
                'AUTH0_TOKEN_USER_TESTER='.Config::get('laravel-auth0.token_user_tester'), 
                'AUTH0_TOKEN_USER_TESTER=', 
                file_get_contents($path)
            ));
    }
}