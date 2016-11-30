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
     * @return \Illuminate\Http\Response           
     */
    public function store(Request $request)
    {   
    	$this->validationForStoreAction($request, ['name' => 'required|string']);
    	$school = School::create($request->all());

        return $this->response->created("/schools/{$school->id}", $school);
    }

    /**
     * Lista escolas
     * 
     * @param  Request $request 
     * @return \Illuminate\Http\Response           
     */
    public function index(Request $request)
    {
        return $this->parseMultiple(new School,['name']);
    }

    /**
     * Dados da escola
     * 
     * @param  Request $request 
     * @param  int $id
     *  
     * @return \Illuminate\Http\Response           
     */
    public function show(Request $request, $id)
    {
        return $this->apiHandler
            ->parseSingle(New School, $id)
            ->getResult();
    }

    /**
     * Remove uma escola.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $school = School::findOrFail($id);
        $school->delete();

        return $this->response->noContent();
    }

    /**
     * Atualiza os dados de uma escola.
     *
     * @param  Request $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validationForUpdateAction($request, ['name' => 'required|string']);

        $school = School::findOrFail($id);
        $school->update($request->all());

        return $school;
    }
} 
