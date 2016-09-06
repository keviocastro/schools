<?php

namespace App\Http\Controllers;

use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;
use Dingo\Api\Routing\Helpers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Marcelgwerder\ApiHandler\ApiHandler;

class Controller extends BaseController
{
    use AuthorizesRequests, 
        AuthorizesResources, 
        DispatchesJobs, 
        ValidatesRequests,
        Helpers;

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

    /**
     * Validation for store action 
     * 
     * @param  [type] $request   
     * @param  array  $rules     See https://laravel.com/docs/5.3/validation
     * @param  string $error_msg 
     * @return void
     *
     * @throws  Dingo\Api\Exception\StoreResourceFailedException
     */
    public function validationForStoreAction($request, array $rules, $error_msg='Could not create new resource.')
    {
        $validator = app('validator')->make($request->all(), $rules);

        if ($validator->fails()) {
            throw new StoreResourceFailedException($error_msg, $validator->errors());
        }
    }

    /**
     * Validation for update action 
     * 
     * @param  [type] $request   
     * @param  array  $rules     See https://laravel.com/docs/5.3/validation
     * @param  string $error_msg 
     * @return void
     *
     * @throws  Dingo\Api\Exception\UpdateResourceFailedException
     */
    public function validationForUpdateAction($request, array $rules, $error_msg='Could not update resource.')
    {
        $validator = app('validator')->make($request->all(), $rules);

        if ($validator->fails()) {
            throw new UpdateResourceFailedException($error_msg, $validator->errors());
        }
    }
}
