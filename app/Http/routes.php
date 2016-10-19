<?php
$api = app('Dingo\Api\Routing\Router');
Route::get('/', function () {
	return view('welcome');
});
$api->version('v1', function ($api) {
	
	$api->group(['middleware' => 'cors'], function ($api) {
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
			
			// School classes
			$api->get('school-classes', 'App\Http\Controllers\SchoolClassController@index');
			$api->post('school-classes', 'App\Http\Controllers\SchoolClassController@store');
			$api->get('school-classes/{school_class_id}', 'App\Http\Controllers\SchoolClassController@show');
			$api->put('school-classes/{school_class_id}', 'App\Http\Controllers\SchoolClassController@update');
			$api->delete('school-classes/{school_id}', 'App\Http\Controllers\SchoolClassController@destroy');
			
			// Lessons
			$api->get('lessons/per-day', 'App\Http\Controllers\LessonController@listPerDay');
			$api->get('lessons', 'App\Http\Controllers\LessonController@index');
			$api->post('lessons', 'App\Http\Controllers\LessonController@store');
			$api->get('lessons/{lesson_id}', 'App\Http\Controllers\LessonController@show');
			$api->put('lessons/{lesson_id}', 'App\Http\Controllers\LessonController@update');
			$api->delete('lessons/{lesson_id}', 'App\Http\Controllers\LessonController@destroy');
			
			// Attendance records
			$api->get('attendance-records', 'App\Http\Controllers\AttendanceRecordController@index');
			$api->post('attendance-records', 'App\Http\Controllers\AttendanceRecordController@store');
			$api->get('attendance-records/{id}', 'App\Http\Controllers\AttendanceRecordController@show');
			$api->put('attendance-records/{id}', 'App\Http\Controllers\AttendanceRecordController@update');
			$api->delete('attendance-records/{id}', 'App\Http\Controllers\AttendanceRecordController@destroy');
			
			// Assessments
			$api->get('assessments', 'App\Http\Controllers\AssessmentController@index');
			

			//OCCURRENCE
			$api->get('occurences', 'App\Http\Controllers\OccurenceController@index');
			$api->post('occurences', 'App\Http\Controllers\OccurenceController@store');
			$api->delete('occurences/{id}', 'App\Http\Controllers\OccurenceController@destroy');
			$api->put('occurences/{id}', 'App\Http\Controllers\OccurenceController@update');
		});
	});

	$api->get('health', 'App\Http\Controllers\ApiController@health');
	$api->get('health/db', 'App\Http\Controllers\ApiController@healthDatabase');
});