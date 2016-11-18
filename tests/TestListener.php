<?php 

namespace Tests;

use PHPUnit_Framework_BaseTestListener;
use PHPUnit_Framework_TestSuite;

class TestListener extends PHPUnit_Framework_BaseTestListener
{
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