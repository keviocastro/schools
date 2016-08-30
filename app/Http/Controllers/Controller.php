<?php

namespace App\Http\Controllers;

use Dingo\Api\Routing\Helpers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Marcelgwerder\ApiHandler\ApiHandler;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    /**
     * Ppi to work with filter parameters, search, sort and limits methodos listing.
     *
     * @var ApiHandler
     */
    protected $apiHandler;

   /**
     * Create a new controller instance.
     *
     * @param  ApiHandler  $apiHandler
     * @return void
     */
    public function __construct(ApiHandler $apiHandler)
    {
        $this->apiHandler = $apiHandler;
    }
}
