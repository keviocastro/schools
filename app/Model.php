<?php
namespace App;


use App\Observers\OwnerResourceObsever;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel
{
    public static function boot()
    {	
    	$class = get_called_class();
    	$class::observe(OwnerResourceObsever::class);

    	parent::boot();
    }

}