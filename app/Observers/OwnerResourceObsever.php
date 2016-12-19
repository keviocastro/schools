<?php
namespace App\Observers;

use Illuminate\Support\Facades\Auth;

class OwnerResourceObsever
{
    public function creating($model){
        if(Auth::user()){
            $model->created_by = Auth::user()->sub;
        }
    }
    public function updating($model){
        if(Auth::user()){
            $model->updated_by = Auth::user()->sub;
        }
    }
    public function deleted($model){
        if(Auth::user()){
            $model->deleted_by = Auth::user()->sub;
//            dd('delete: ',$model->id,$model->deleted_by);
        }
    }
}