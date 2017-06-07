<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Cache;
use Auth0\Login\LaravelCacheWrapper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            \Auth0\Login\Contract\Auth0UserRepository::class,
            \Auth0\Login\Repository\Auth0UserRepository::class);

        $this->app->bind(
        '\Auth0\SDK\Helpers\Cache\CacheHandler',
        function() {
            static $cacheWrapper = null; 
            if ($cacheWrapper === null) {
                $cache = Cache::store();
                $cacheWrapper = new LaravelCacheWrapper($cache);
            }
            return $cacheWrapper;
        });

    }
}
