<?php

namespace App\Http\Controllers;

use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;
use Dingo\Api\Routing\Helpers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Input;
use Marcelgwerder\ApiHandler\ApiHandler;

class Controller extends BaseController
{
    use AuthorizesRequests, 
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
     * Retorna um objeto de resultado padrão para api de multiplos resultados. 
     *
     * @param  mixed            $queryBuilder          Some kind of query builder instance
     * @param  array            $fullTextSearchColumns Columns to search in fulltext search
     * @param  array|boolean    $queryParams           A list of query parameter
     * @return Result
     */
    public function parseMultiple($queryBuilder, $fullTextSearchColumns = array(), $queryParams = false)
    {
        if ($queryParams === false) {
            $queryParams = Input::get();
        }


        // Se não remover apiHandler utiliza como filter.
        // _limit e _offset foram removidos porque é utilizado
        // o parametro _per_page para realizar a paginação.
        $notAFilter = [
            '_page', 
            '_per_page', 
            'XDEBUG_SESSION_START', 
            'XDEBUG_SESSION_STOP',
            '_limit',
            '_offset'
        ];

        foreach ($notAFilter as $value) {
            if (!empty($queryParams[$value])) {
                unset($queryParams[$value]);
            }
        }

        $result = $this->apiHandler->parseMultiple($queryBuilder, 
            $fullTextSearchColumns, $queryParams);
        
        $result = $result->getBuilder()->paginate(
            Input::get('_per_page', null), 
            $columns = ['*'], 
            $pageName = '_page', 
            $page = null);        

        return $result;
    }

    /**
     * Validation for store action queryParams
     * 
     * @param  Illuminate\Http\Request $request   
     * @param  array  $rules     See https://laravel.com/docs/5.3/validation
     * @param  string $error_msg
     * @param  bool $accept_items_array When an array of items is accepted, as shown below
     *                                  [
     *                                      ['attribute_1' => 'value_1', 'attribute_2' => 'value_2'],
     *                                      ['attribute_1' => 'value_1', 'attribute_2' => 'value_2']
     *                                  ]
     * @return void
     *
     * @throws  Dingo\Api\Exception\StoreResourceFailedException
     */
    public function validationForStoreAction($request, array $rules, $error_msg='', $accept_items_array=false)
    {
        $error_msg = empty($error_msg) ? 'Could not create new resource.' : $error_msg;

        if ($accept_items_array) {
            $inputs = $this->makeMultipleInputData();
            
            collect($inputs)->map(function($item, $key) use ($rules,$error_msg){
                $validator = app('validator')->make($item, $rules);
                if ($validator->fails()) {
                    throw new StoreResourceFailedException($error_msg, $validator->errors());
                }
            });
        }else{
            $validator = app('validator')->make($request->all(), $rules);
            if ($validator->fails()) {
                throw new StoreResourceFailedException($error_msg, $validator->errors());
            }
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

    /**
     * Validation for list actions
     * 
     * @param  array  $rules     See https://laravel.com/docs/5.3/validation
     * @param  string $error_msg $error_msg 
     * @return void
     *
     * @throws  Dingo\Api\Exception\UpdateResourceFailedException
     */
    public function validationForListAction(array $rules, $error_msg='Invalid parameters.')
    {
        $payload = request()->all();
        $validator = app('validator')->make($payload, $rules);

        if ($validator->fails()) {
            throw new ResourceException($error_msg, $validator->errors());
        }
    }

    /**
     * Checks for multiple records on request
     * 
     * Example for true: [['name' => 'first name', 'age' => 30], ['name' => 'second name', 'age' => 20]]
     * Example for false: ['name' => 'first name', 'age' => 30]
     * 
     * @return bool
     */
    public function checkMultipleInputData()
    {
        $inputs = request()->all();
        return count($inputs, COUNT_RECURSIVE) == count($inputs) ? false : true;
    }

    /**
     * Cria sempre um array de inputs da requisição.
     * 
     * @example ['name' => 'first name', 'age' => 30] retorna [['name' => 'first name', 'age' => 30]]
     *          [['name' => 'first name', 'age' => 30],[...]] retorna identico.  
     * 
     * @return array of parameters
     */
    public function makeMultipleInputData()
    {
        if ($this->checkMultipleInputData()) {
            return request()->json()->all();
        }else{
            return [request()->json()->all()];
        }
    }
}
