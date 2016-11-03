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

    /**
     * Get class students
     * 
     * @Relation
     * 
     * @return 
     */
    public function students()
    {
        return $this->hasMany('App\SchoolClassStudent')
            ->with('student');
    }
}
