<?php
$api = app('Dingo\Api\Routing\Router');
Route::get('/', function () {
    return view('welcome');
});

$api->version('v1', function ($api) {

	 $api->group(['middleware' => 'auth0.jwt'], function ($api) {
        
        // Auth
		$api->post('auth/request-access', 'App\Http\Controllers\Auth\AuthController@requestAccess');
		
		// Schools
		$api->get('schools', 'App\Http\Controllers\SchoolController@index');
		$api->post('schools', 'App\Http\Controllers\SchoolController@store');
    
    });

	$api->get('health', 'App\Http\Controllers\ApiController@health');
	$api->get('health/db', 'App\Http\Controllers\ApiController@healthDatabase');
});

