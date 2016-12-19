<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Registros de presença
 */
class AttendanceRecord extends Model
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
        'lesson_id', 'student_id', 'presence'
    ];

    /**
     * Get a lesson record
     *
     * @Relation
     * 
     * @return Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function lesson()
    {
    	return $this->belongsTo('App\Lesson');
    }

    /**
     * Get a subject record
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subject()
    {
        return $this->belongsTo('App\Subject');
    }
}
