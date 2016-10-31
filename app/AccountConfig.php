<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountConfig extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'value', 'default'
    ];

    /**
     * Percentual máximo de faltas que um aluno 
     * pode ter para não ser reprovado por falta.
     * 
     * @return float
     */
    public static function getPercentageAbsencesReprove()
    {
    	$config = self::where('name', 'percentage_absences_reprove')->first();
		$value = null;
    	if ($config) {
    		$value = empty($config->value) ? $config->default : $config->value;  
    	}

    	return (float) $value;
    }
}
