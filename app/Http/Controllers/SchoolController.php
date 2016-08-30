<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\School;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use Marcelgwerder\ApiHandler\ApiHandler;

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

        return School::paginate();

    	$attributes = $request->all();
    	$school = School::create($attributes);

        return $this->response->created("/schools/{$school->id}", $school);
    }

    /**
     * Lista escolas
     * 
     * @param  Request $request 
     * @return \Illuminate\Http\JsonResponse           
     */
    public function index(Request $request)
    {
        $result = $this->apiHandler->parseMultiple(new School);
        
        return $result->getBuilder()->paginate();
    }
} 
