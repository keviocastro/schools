<?php

namespace App\Http\Controllers;

use App\AccountConfig;
use App\Http\Requests;
use App\Http\Transformers\AccountConfigTransformer;
use Illuminate\Http\Request;

class AccountConfigController extends Controller
{
    public function show()
    {
    	$configs = AccountConfig::all();
    	return $this->collection($configs, new AccountConfigTransformer);
    }
}
