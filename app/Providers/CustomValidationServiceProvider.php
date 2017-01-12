<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\DateValidation;

class CustomValidationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services. 
     *
     * @return void
     */
    public function boot()
    {
        $this->app->validator->resolver(function($translator, $data, $rules, $message){
            return new DateValidation($translator, $data, $rules, $message);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
