<?php 

namespace App\Http\Transformers;


use Dingo\Api\Http\Request;
use Dingo\Api\Transformer\Binding;
use Dingo\Api\Contract\Transformer\Adapter;

class AccountConfigTransformer implements Adapter
{
    public function transform($response, $transformer, Binding $binding, Request $request)
    {
        dd($transformer);
    }
}