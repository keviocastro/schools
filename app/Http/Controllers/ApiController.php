<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function health()
    {
    	return $this->response->array(['health' => 'green']);
    }
}
