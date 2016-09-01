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
		$api->get('schools/{school_id}', 'App\Http\Controllers\SchoolController@show');
		$api->post('schools', 'App\Http\Controllers\SchoolController@store');
		$api->put('schools/{school_id}', 'App\Http\Controllers\SchoolController@update');
		$api->delete('schools/{school_id}', 'App\Http\Controllers\SchoolController@destroy');
    
    	// Persons
		$api->get('students', 'App\Http\Controllers\StudentController@index');
    	
    	
    });

	$api->get('health', 'App\Http\Controllers\ApiController@health');
	$api->get('health/db', 'App\Http\Controllers\ApiController@healthDatabase');
});

