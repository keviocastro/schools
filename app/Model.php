<?php

namespace App;

use App\Observers\OwnerResourceObsever;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use App\Services\Validation;

/**
 * @todo  Remover todos os hidden para colunas  
 *        'deleted_at', 'updated_at', 'created_at', 'created_by', 'deleted_by', 'updated_by'
 */
class Model extends EloquentModel
{
    /**
     * Class abistrato do modelo (monitora os eventos)
     */
    public static function boot()
    {	
    	$class = get_called_class();
    	$class::observe(OwnerResourceObsever::class);

    	parent::boot();
    }

}