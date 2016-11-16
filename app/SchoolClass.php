<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolClass extends Model
{
     use SoftDeletes;

     /**
     * 
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['deleted_at'];
    
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
     * Ano da turma 
     *
     * @Relation
     */
    public function grade()
    {
    	return $this->belongsTo('App\Grade');
    }

    /**
     * Turno da turma 
     *
     * @Relation
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany 
     */
    public function students()
    {
        return $this->hasMany('App\SchoolClassStudent')
            ->with('student');
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
}
