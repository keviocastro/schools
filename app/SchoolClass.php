<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Turmas (1º Ano A, Jardim I - A, Jardim I - B)
 */
class SchoolClass extends Model
{
     use SoftDeletes;

     /**
     * 
     * The attributes that should be hidden for arrays.
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
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'identifier', 
        'grade_id', 
        'shift_id',
        'school_calendar_id',
        'school_id'];

    /**
     * Turno da turma 
     *
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shift()
    {
    	return $this->belongsTo('App\Shift');
    }

    /**
     * Estudantes da aula
     * 
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function students()
    {
        return $this->belongsToMany('App\Student')->with('person');
    }

    /**
     * Ano letivo da turma
     * 
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function schoolCalendar()
    {
        return $this->belongsTo('App\SchoolCalendar');
    }

    /**
     * Disciplinas da turma
     * 
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subjects()
    {
        return $this->belongsToMany('App\Subject', 'school_class_subjects');
    }

    /**
     * Nível escolar da turma (Ex.: 1º Ano, 2º Ano)
     *
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function grade()
    {
       return $this->belongsTo('App\Grade');
    }

    /**
     * Escola que a turma pertence
     *
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school()
    {
        return $this->belongsTo('App\School');
    }

}
