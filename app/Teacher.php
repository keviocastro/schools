<?php

namespace App;

class Teacher extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['person_id'];

    /**
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function grade()
    {
        return $this->belongsTo('App\Grade', 'grade_id');
    }
    
    /**
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function schoolCalendar()
    {
        return $this->belongsTo('App\SchoolCalendar', 'school_calendar_id');
    }
}
