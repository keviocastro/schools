<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
     use SoftDeletes;

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
