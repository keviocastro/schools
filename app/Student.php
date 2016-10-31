<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
     use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    
    /**
     * 
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['person_id', 'class_id'];

    /**
     * Get the person record associated with the student.
     * 
     * @Relation
     *
     * @return App\Person
     */
    public function person()
    {
        return $this->belongsTo('App\Person');
    }

    /**
     * Get the school class record associated with the student.
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
     * Get the student responsible
     *
     * @Relation
     */
    public function responsibles()
    {
        return $this->hasMany('App\StudentResponsible');
    }

    /**
     * Obtem a lista de registros de chamadas do aluno
     *
     * @Relation
     */
    public function attendanceRecords()
    {
        return $this->hasMany('App\AttendanceRecord');
    }


    /**
     * Resumo de ausência do aluno em um ano letivo
     * 
     * @param  string $school_class_id  id da turma
     * @param  string $subject_id       id da disciplina
     * 
     * @return array
     */
    public function absenceSummaryYear($school_class_id, $subject_id)
    {
        $total_absences = $this->attendanceRecords()
            ->join('lessons', 'lessons.id', '=', 'attendance_records.lesson_id')
            ->join('school_classes', 'school_classes.id', '=', 'lessons.school_class_id')
            ->where('lessons.school_class_id', $school_class_id)
            ->where('lessons.subject_id', $subject_id   )
            ->count();

        $total_lessons_year = Lesson::
            where('school_class_id', $school_class_id)
            ->where('subject_id', $subject_id)
            ->count();

        return [
            'percentage_absences_reprove' => AccountConfig::getPercentageAbsencesReprove(),
            'total_lessons_year' =>  $total_lessons_year,
            'total_absences_year' => $total_absences,
        ];

        return $totalAbsences;
    }
}
