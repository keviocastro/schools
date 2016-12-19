<?php
namespace App\Observers;

use Illuminate\Support\Facades\Auth;

class OwnerResourceObsever
{
    /**
     * @param $model
     *
     * @return void
     */
    public function creating($model)
    {
        if(Auth::user()) {
            $model->created_by = Auth::user()->sub;
        }
    }

    /**
     * @param $model
     *
     * @return void
     */
    public function updating($model)
    {
        if(Auth::user()) {
            $model->updated_by = Auth::user()->sub;
        }
    }

    /**
     * @param $model
     *
     * @return void
     */
    public function deleting($model)
    {
        if(Auth::user()) {
            $model->deleted_by = Auth::user()->sub;
        }
    }
}