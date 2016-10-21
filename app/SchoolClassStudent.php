<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolClassStudent extends Model
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
        'student_id'];


    /**
     * Get student
     * 
     * @Relation 
     * 
     * @return App\Student
     */
    public function student()
    {
        return $this->belongsTo('App\Student');
    }
}
