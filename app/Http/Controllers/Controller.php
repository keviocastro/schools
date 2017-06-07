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
     * Parametros customizados:
     *  _group_by =         Agrupa o resultado por 1 ou mais valores de atributos (separados por virgula) que contenham no resultado.
     *  Exemplo: api/student-grades/?_group_by=student_id,school_class_id
     *      [
     *          ...
     *          "data" => [
     *              "1-3": [
     *                  [
     *                      student_id: 1,
     *                      school_class: 3
     *                  ],
     *                  [
     *                      ....
     *                  ]
     *              ],
     *              "2-3": [
     *                  [
     *                      student_id: 2,
     *                      school_class: 3
     *                  ]
     *              ]
     *          ]
     *      ]
     * 
     *  _group_by_count =   Funciona em combinação com _group_by. 
     *                      Se true, retorna somente a quantidade de registros existentes no grupo
     *                      definido em _group_by.
     * Exemplo:
     *         [
     *          ...
     *          "data" => [
     *              "1-3": 2,
     *              "2-3": 1
     *          ]
     *      ]
     * 
     * Retorna um objeto de resultado padrão para api de multiplos resultados. 
     *
     * @param  mixed            $queryBuilder          Some kind of query builder instance
     * @param  array            $fullTextSearchColumns Columns to search in fulltext search
     * @param  array|boolean    $queryParams           A list of query parameter
     * @return Result
     */
    public function parseMultiple($queryBuilder, 
        $fullTextSearchColumns = array(), 
        $queryParams = false)
    {

        if ($queryParams === false) {
            $queryParams = Input::get();
            $group_by = empty($queryParams['_group_by']) ? false : $queryParams['_group_by'] ;
            $group_by_count = empty($queryParams['_group_by_count']) ? false : true;
        }
        
        $queryParams = $this->filterQueryParams($queryParams);

        $result = $this->apiHandler->parseMultiple($queryBuilder, 
            $fullTextSearchColumns, $queryParams);
        
        $result = $result->getBuilder()->paginate(
            Input::get('_per_page', null), 
            $columns = ['*'], 
            $pageName = '_page', 
            $page = null)->toArray();

        return $this->parseGroupBy($result, $group_by, $group_by_count);
    }

    /**
     * Filter valid params
     *
     * @return array 
     */
    public function filterQueryParams($queryParams)
    {
        
        // Se não remover apiHandler utiliza como filter.
        // _limit e _offset foram removidos porque é utilizado
        // o parametro _per_page para realizar a paginação.
        $notAFilter = [
            '_page', 
            '_per_page', 
            'XDEBUG_SESSION_START', 
            'XDEBUG_SESSION_STOP',
            '_limit',
            '_offset',
            '_group_by',
            '_group_by_count',
            'XDEBUG_PROFILE'
        ];

        foreach ($notAFilter as $value) {
            if (!empty($queryParams[$value])) {
                unset($queryParams[$value]);
            }
        }

        return $queryParams;
    }

    /**
     * Parse _group_by and _group_by_count params
     *
     * @param array $result
     * @param array $queryParams
     * 
     * @return array $result grouped if applicable
     */
    public function parseGroupBy($result, $groupBy, $groupByCount)
    {
        
        if($groupBy){
            if($result['total'] > 0){
                
                $groupBy = explode(',', $groupBy);
                $groupByCount =  $groupByCount;

                // If attribute exists in the results
                $attributes = collect(array_keys($result['data'][0]));
                $groupBy = collect($groupBy);
                $groupBy->each(function($group) use ($attributes){
                    if ( !$attributes->search($group) )
                        throw new ResourceException(
                            "The attribute ($group) defined in the _group_by parameter does not exist in the result set."
                            );
                });

                $result['data'] = collect($result['data'])->groupBy(function($item) use ($groupBy){
                    $groupValues = $groupBy->reduce(function($carry, $group) use ($item){
                        return empty($carry) ? $item[$group] : $carry .'-'. $item[$group]; 
                    });
                    return  $groupValues;
                });
                
                if($groupByCount){
                    $result['data'] = $result['data']->mapWithKeys(function($item, $key){
                        return [$key => count($item)];
                    });
                }
            }
        }

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
     * @param  Request $request   
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
            return request()->input();
        }else{
            return [request()->input()];
        }
    }
}
