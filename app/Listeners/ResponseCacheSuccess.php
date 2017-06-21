<?php

namespace App\Listeners;

use Spatie\ResponseCache\Events\ResponseCacheHit;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResponseCacheSuccess
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ResponseCacheHit  $event
     * @return void
     */
    public function handle(ResponseCacheHit $event)
    {
    }
}
