<?php

namespace App\Http\Middleware;

use Closure;

    class ResponseCache
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
        $key = $request->fullUrl();
        $keyResponseContent = $key.':response:content';
        $keyResponseStatus =  $key.':response:status';
        $keyResponseHeaders = $key.':response:headers';
        

        if(\Cache::has($keyResponseContent)){
            return response(\Cache::get($keyResponseContent), 
                \Cache::get($keyResponseStatus),
                \Cache::get($keyResponseHeaders)
                );
        }
            
        $response = $next($request);
        $cachingTime = \Config::get('cache.lifetime');
        \Cache::put($keyResponseContent, $response->getContent(), $cachingTime);
        \Cache::put($keyResponseStatus, $response->getStatusCode(), $cachingTime);
        \Cache::put($keyResponseHeaders, iterator_to_array($response->headers->getIterator()), $cachingTime);

        return $response;
    }
}
