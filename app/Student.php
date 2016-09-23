<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
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
}
