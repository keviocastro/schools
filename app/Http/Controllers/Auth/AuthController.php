<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\RequestAccess;
use App\User;
use Auth0;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

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
            where('auth0_user_id', $auth0_user_id)
            ->first();

        if (!$requestAccess) {
            $requestAccess = RequestAccess::create([
                    'status' => 0, // Pendente
                    'auth0_user_id' => $auth0_user_id
                ]);
        }

        return $requestAccess;
    }
}
