<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
     use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    
    /**
     * 
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['person_id', 'class_id'];

    /**
     * Get the person record associated with the student.
     * 
     * @Relation
     *
     * @return App\Person
     */
    public function person()
    {
        return $this->belongsTo('App\Person');
    }

    /**
     * Get the school class record associated with the student.
     *
     * @Relation
     *
     * @return App\SchoolClass
     */
    public function schoolClass()
    {
        return $this->belongsTo('App\SchoolClass');
    }

    /**
     * Get the student responsible
     *
     * @Relation
     */
    public function responsibles()
    {
        return $this->hasMany('App\StudentResponsible');
    }

    /**
     * Obtem a lista de registros de chamadas do aluno
     *
     * @Relation
     */
    public function attendanceRecords()
    {
        return $this->hasMany('App\AttendanceRecord');
    }
}
