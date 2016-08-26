<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\School;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
	 use Helpers;

    /**
     * Registra escola
     * 
     * @param  Request $request 
     * @return \Illuminate\Http\JsonResponse           
     */
    public function store(Request $request)
    {   
    	$this->validate($request, [
	        'name' => 'required|string',
	    ]);

    	$attributes = $request->all();
    	$school = School::create($attributes);

        return $this->response->created("/schools/{$school->id}", $school);
    }
}
