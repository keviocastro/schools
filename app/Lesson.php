<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Aula
 */
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
     * 
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['deleted_at'];


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

    /**
     * Registros de chamada da aula 
     *
     * @Relation
     * 
     * @return App\AttendanceRecords
     */
    public function attendanceRecords()
    {
        return $this->hasMany('App\AttendanceRecord');
    }

    /**
     * Get students Lesson
     *
     * @param bool Exibir total de aulas do aluno no ano na mesma disciplina
     * 
     * @return array of App\Students
     */
    public function students($totalAbsences=false)
    {
        $students = \App\Student::select('students.*')
            ->join(
                'school_class_students', 
                'school_class_students.student_id', 
                '=', 
                'students.id'
                )
            ->join('people', 'people.id', '=', 'students.person_id')
            ->where('school_class_students.school_class_id', $this->school_class_id)
            ->orderBy('people.name')
            ->with('person', 'responsibles.person');

        if ($totalAbsences) {
            
            $subQuery = "SELECT count(*) FROM attendance_records ".
                        "INNER JOIN lessons ON lessons.id = attendance_records.lesson_id ".
                        "WHERE presence = 0 ".
                            "AND lessons.subject_id = {$this->subject_id} ".
                            "AND attendance_records.student_id = students.id";
                
            $students->addSelect(\DB::raw("($subQuery) as totalAbsences"));
        }
        
        return $students->get();
    }

}
