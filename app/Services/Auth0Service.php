<?php

namespace App\Services;

use Config;
use Auth0\SDK\API\Authentication;
use Auth0\SDK\API\Management;
use Illuminate\Support\Facades\Cache;

class Auth0Service {
    
    public static function getUser($user_id){
        $token = self::getAcessToken();
        $domain = Config::get('laravel-auth0.domain');
        $auth0Api = new Management($token, $domain);
        return $auth0Api->users->get($user_id);
    }
    
    public static function getAcessToken(){
        if(Cache::has('auth0_token_manager_api')){
            $access_token = Cache::get('auth0_token_manager_api');
        }else{
            $domain =  Config::get('laravel-auth0.domain');
            $client_id =  Config::get('laravel-auth0.client_id');;
            $client_secret = Config::get('laravel-auth0.client_secret');
    
            $auth0Api = new Authentication($domain, $client_id, $client_secret);
    
            // getting an access token with client credentials grant
            $response = $auth0Api->client_credentials([
                    'audience' => "https://$domain/api/v2/"
                ]);
            $access_token = $response['access_token'];
            Cache::put('auth0_token_manager_api', $access_token, $response['expires_in']);
        }

        return $access_token;
    }
}