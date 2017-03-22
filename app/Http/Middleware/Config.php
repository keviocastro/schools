<?php

namespace App\Http\Middleware;

use Closure;
use Config as LConfig;

class Config
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /**
         * Para configurar a decodificação de tokens
         * Solucionando esse problema:
         *     Os horários do servidor e do browser do usuário (que gera o token em outra aplicação SPA)
         *     tem pequenas diferença, impossibilitando decodificar um token gerar em uma data acima a 
         *     data atual do servidor de SchoolsApi.
         *
         * Esse atributo configura a classe Firebase\JWT para permitir 
         * uma pequena diferença toleravel
         *
         */
        \Firebase\JWT\JWT::$leeway = LConfig::get('laravel-auth0.jwt_leeway'); // $leeway in seconds
        
        return $next($request);
    }
}
