<?php 

namespace Tests;

use PHPUnit_Framework_BaseTestListener;
use PHPUnit_Framework_TestSuite;

class TestListener extends PHPUnit_Framework_BaseTestListener
{
    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $this->destroyAuthToken();
    }

    public function destroyAuthToken()
    {
        putenv("auth0_token_test=");
    }
}