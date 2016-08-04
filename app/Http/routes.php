<?php
$api = app('Dingo\Api\Routing\Router');
Route::get('/', function () {
    return view('welcome');
});

$api->version('v1', function ($api) {

	 $api->group(['middleware' => 'auth0.jwt'], function ($api) {
        // Endpoints registered here will have the "foo" middleware applied.
		$api->get('auth/request-access', 'App\Http\Controllers\Auth\AuthController@requestAccess');
    });

	$api->get('health', 'App\Http\Controllers\ApiController@health');
	$api->get('health/db', 'App\Http\Controllers\ApiController@healthDatabase');
});

