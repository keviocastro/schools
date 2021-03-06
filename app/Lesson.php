<?php

namespace App;

use Carbon\Carbon;

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
    protected $dates = [
        'deleted_at',
        'start',
        'end'
        ];

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
        'lesson_plan_id',
        'teacher_id'
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
        'updated_by'
    ];

    /**
     * Get a teacher
     *
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function teacher()
    {
        return $this->belongsTo('App\Teacher');
    }

    /**
     * Get a school class record
     *
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
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
     * Get a lesson plan record
     * 
     * @Relation
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lessonPlan()
    {
        return $this->belongsTo('App\LessonPlan');
    }

    /**
     * Registros de chamada da aula 
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
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

    public static function queryDaysBetweenDates(Carbon $startDate, Carbon $endDate){
        $startDate = $startDate->format('Y-m-d');
        $endDate = $endDate->format('Y-m-d');

        $query = "select * from (
            select adddate('$startDate',t4*10000 + t3*1000 + t2*100 + t1*10 + t0) day 
            from (select 0 t0 
                    union select 1 
                    union select 2 
                    union select 3 
                    union select 4 
                    union select 5 
                    union select 6 
                    union select 7 
                    union select 8 
                    union select 9) t0, 
                (select 0 t1 
                    union select 1 
                    union select 2 
                    union select 3 
                    union select 4 
                    union select 5 
                    union select 6 
                    union select 7 
                    union select 8 
                    union select 9) t1, 
                (select 0 t2 
                    union select 1 
                    union select 2 
                    union select 3 
                    union select 4 
                    union select 5 
                    union select 6 
                    union select 7 
                    union select 8 
                    union select 9) t2, 
                (select 0 t3 
                    union select 1 
                    union select 2 
                    union select 3 
                    union select 4 
                    union select 5 
                    union select 6 
                    union select 7 
                    union select 8 
                    union select 9) t3, 
                (select 0 t4 
                    union select 1 
                    union select 2 
                    union select 3 
                    union select 4 
                    union select 5 
                    union select 6 
                    union select 7 
                    union select 8 
                    union select 9) t4) v 
            where day between '$startDate' and '$endDate'";

        return $query;
    }

}
