<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'identifier', 
        'grade_id', 
        'shift_id',
        'school_calendar_id'];

    /**
     * Get a grade record associate with the shcool class 
     *
     * @Relation
     */
    public function grade()
    {
    	return $this->belongsTo('App\Grade');
    }

    /**
     * Get a shift record associate with the shcool class 
     *
     * @Relation
     */
    public function shift()
    {
    	return $this->belongsTo('App\Shift');
    }
}
