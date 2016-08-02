<?php
$api = app('Dingo\Api\Routing\Router');
Route::get('/', function () {
    return view('welcome');
});

$api->version('v1', ['middleware' => 'auth0.jwt'], function ($api) {

	$api->get('auth/request-access', 'App\Http\Controllers\Auth\AuthController@requestAccess');
});

