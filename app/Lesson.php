<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'school_class_id', 
    	'subject_id', 
    	'start', 
    	'end',
    	];


    /**
     * Get a school class record
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
     * Get a subject record
     * 
     * @Relation
     * 
     * @return App\Subject
     */
    public function subject()
    {
    	return $this->belongsTo('App\Subject');
    }
}
