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
     * @Relation
     */
    public function person()
    {
        return $this->belongsTo('App\Person');
    }

    /**
     * Get the school class record associated with the student.
     *
     * @Relation
     */
    public function schoolClass()
    {
        return $this->belongsTo('App\SchoolClass');
    }
}
