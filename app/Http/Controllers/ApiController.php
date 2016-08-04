<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\RequestAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function health()
    {
    	// To verify that the app is ready. 
		RequestAccess::first(); 
    	return $this->response->array(['health' => 'green']);
    }

    public function healthDatabase()
    {
    	// To verify that the database is ready
    	Db::select(Db::raw("SELECT 1")); 
    	return $this->response->array(['health' => 'green']);
    }
}
