<?php

namespace App\Http\Controllers;

use App\AccountConfig;
use App\Http\Requests;
use App\Http\Transformers\AccountConfigTransformer;
use Illuminate\Http\Request;

class AccountConfigController extends Controller
{
	/**
     * Exibir uma lista das configurações
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	return AccountConfig::all();
    }

    /**
     * Atualiza os dados da configuração.
     *
     * @param  Request $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validationForUpdateAction($request, [
        	'value' => 'required|numeric'
        	]);

        $config = AccountConfig::findOrFail($id);
        $config->update($request->only('value'));

        return $config;
    }
}
