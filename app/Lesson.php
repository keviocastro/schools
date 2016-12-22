<?php

namespace App;

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
    protected $hidden = ['deleted_at',
        'updated_at',
        'created_at',
        'created_by',
        'deleted_by',
        'updated_by',
        'teacher_id'
    ];


    /**
     * Get a school class record
     *
     * @Relation
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
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
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
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
     * @return Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function attendanceRecords()
    {
        return $this->hasMany('App\AttendanceRecord');
    }

    /**
     * Get students Lesson
     *
     * @return \Illuminate\Database\Eloquent\Collection of App\Students
     */
    public function students()
    {
        $students = \App\Student::select('students.*')
            ->join(
                'school_class_student', 
                'school_class_student.student_id', 
                '=', 
                'students.id'
                )
            ->join('people', 'people.id', '=', 'students.person_id')
            ->where('school_class_student.school_class_id', $this->school_class_id)
            ->orderBy('people.name')
            ->with('person', 'responsibles.person');

        return $students->get();
    }

    /**
     * Calcula a quantidade de aulas no ano 
     * de uma disciplina em uma turma.
     * O ano é obtido a partir da turma
     * 
     * @param  string $school_class_id  id da turma
     * @param  string $subject_id       id da disciplina
     * @return int 
     */
    public static function totalLessonsInYear($school_class_id, $subject_id)
    {
        return Lesson::
            where('school_class_id', $school_class_id)
            ->where('subject_id', $subject_id)
            ->count();
    }

}
