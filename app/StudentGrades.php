<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentGrades extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['grade','student_id','subject_id','assessment_id','owner_person_id','school_class_id'];
    
    /**
     * @Relation
     * 
     * @return App\Student
     */
    public function student()
    {
        return $this->belongsTo('App\Student', 'student_id');
    }
    /**
     * @Relation
     * 
     * @return App\Subject
     */
    public function subject()
    {
        return $this->belongsTo('App\Subject', 'subject_id');
    }
    /**
     * @Relation
     * 
     * @return App\Assessment
     */
    public function assessment()
    {
        return $this->belongsTo('App\Assessment', 'assessment_id');
    }
    /**
     * @Relation
     * 
     * @return App\Person
     */
    public function ownerPerson()
    {
        return $this->belongsTo('App\Person', 'owner_person_id');
    }
    /**
     * @Relation
     * 
     * @return App\SchoolClass
     */
    public function schoolClass()
    {
        return $this->belongsTo('App\SchoolClass', 'school_class_id');
    }
}
