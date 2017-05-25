<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\RequestAccess;
use App\User;
use App\Person;
use Auth0;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    

    /**
     * Registra um solicitação de acesso ao sistema
     * 
     * @param  Request $request 
     * @return \Illuminate\Http\JsonResponse           
     */
    public function requestAccess(Request $request)
    {   
        $auth0_user_id = Auth0::jwtuser()->sub;
        $requestAccess = RequestAccess::
            where('user_id', $auth0_user_id)
            ->first();

        if (!$requestAccess) {
            $requestAccess = RequestAccess::create([
                    'status' => 0, // Pendente
                    'user_id' => $auth0_user_id
                ]);
        }

        return $requestAccess;
    }

    /**
     * Exibe os dados do usuário logado 
     * 
     * @param  Request $request 
     * @return \Illuminate\Http\JsonResponse 
     */
    public function showUser(Request $request)
    {
        $user_id = Auth0::jwtuser()->sub; dd(Auth0::jwtuser());
        $person = Person::select()->with('student', 'teacher');

        $result = $this->apiHandler
            ->parseSingle($person, ['user_id' => $user_id])
            ->getResultOrFail();
        
        return $result;
    }
}
