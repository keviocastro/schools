<?php

namespace App;

/**
 * Disciplinas existentes para um turma
 */
class SchoolClassSubject extends Model
{

    /**
     * 
     * Os atributos que devem ser ocultados quando a entidade
     * está convertida em array.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
        'created_by',
        'deleted_by',
        'updated_by'
        ];
    
    /**
     * Atributos que devem ser convertidos para datas.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Atributos que podem ser alterados
     *
     * @var array
     */
    protected $fillable = [
        'school_class_id', 
        'subject_id', 
        ];
}
