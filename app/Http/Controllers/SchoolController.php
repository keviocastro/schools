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
      * Valida parametros da requisição do recurso de escolas
      * 
      * @param  Request $request 
      * @return void
      */
     public function validation(Request $request)
     {
         $this->validate($request, [
            'name' => 'required|string',
        ]);
     }

    /**
     * Registra escola
     * 
     * @param  Request $request 
     * @return \Illuminate\Http\Response           
     */
    public function store(Request $request)
    {   
    	$this->validation($request);
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
        $result = $this->apiHandler->parseMultiple(new School);
        
        return $result->getBuilder()->paginate();
    }

    /**
     * Dados da escola
     * 
     * @param  Request $request 
     * @param  int $school_id
     *  
     * @return \Illuminate\Http\Response           
     */
    public function show(Request $request, $school_id)
    {
        return School::findOrFail($school_id);
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
        $this->validation($request);

        $school = School::findOrFail($id);
        $school->update($request->all());

        return $school;
    }
} 
