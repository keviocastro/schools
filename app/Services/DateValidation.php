<?php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Validator;

class DateValidation extends Validator{

    /**
     * @todo Alterar mensagem de erro
     *
     * Valida parametro com data menor ou igual a valor de outro parametro
     *
     * @param $attribute, $value, $parameters
     * @return bool
     */
    public function validateDateGreaterOrEquals($attribute, $value, $parameters)
    {
        $dateOne = new Carbon($value);
        $dateTwo = new Carbon( Input::get($parameters[0]));

        if($dateOne->gte($dateTwo))
        {
            return true;
        }
    }

    /**
     * @todo Alterar mensagem de erro
     *
     *  Valida parametro com data menor ou igual a valor de outro parametro
     *
     * @param $attribute, $value, $parameters
     * @return bool
     */
    public function validateDateLessOrEquals($attribute, $value, $parameters)
    {
        $dateOne = new Carbon($value);
        $dateTwo = new Carbon( Input::get($parameters[0]));

        if($dateOne->lte($dateTwo))
        {
            return true;
        }
    }
}