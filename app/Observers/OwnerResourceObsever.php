<?php
namespace App\Observers;

use Illuminate\Support\Facades\Auth;

class OwnerResorceObsever
{
    public function creating($model){
        if(Auth::user()){
            $model->created_by = Auth::user()->sub;
        }
    }
}