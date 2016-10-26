<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceRecord extends Model
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
        'lesson_id', 'student_id', 'presence'
    ];

    /**
     * Get a lesson record
     *
     * @Relation
     * 
     * @return App\SchoolClass
     */
    public function lesson()
    {
    	return $this->belongsTo('App\Lesson');
    }
}
