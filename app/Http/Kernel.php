<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \Spatie\ResponseCache\Middlewares\CacheResponse::class
        ],

        'api' => [
            \Spatie\ResponseCache\Middlewares\CacheResponse::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        // 'auth' => \App\Http\Middleware\Authenticate::class,
        // 'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        // 'can' => \Illuminate\Foundation\Http\Middleware\Authorize::class,
        // 'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        // 'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,

        'auth0.jwt' => \Auth0\Login\Middleware\Auth0JWTMiddleware::class,
        'config' => \App\Http\Middleware\Config::class,
        'doNotCacheResponse' => \Spatie\ResponseCache\Middlewares\DoNotCacheResponse::class,
        'responseCache' => \Spatie\ResponseCache\Middlewares\CacheResponse::class,
    ];
}
