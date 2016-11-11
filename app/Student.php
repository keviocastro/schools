<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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
     * Notas do aluno
     *
     * @Relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function studentGrades()
    {
        return $this->hasMany('App\StudentGrade');
    }

    /**
     * Médias aritiméticas das notas do aluno de um ano 
     * por disciplina (subject)
     *
     * @param integer $school_calendar_id         Opctional. Filtro
     * @param integer $school_calendar_phase_id   Opctional. Filtro.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function studentGradesAverage(
        $school_calendar_id=false,
        $school_calendar_phase_id=false
        ){

        $query = $this->studentGrades()
                ->select('student_grades.subject_id',
                    DB::raw('round(avg(grade),1) average'))
                ->groupBy('subject_id')
                ->with('subject', 'assessment');
        
        if ($school_calendar_phase_id) {
            $query
            ->join('assessments', 
                'assessments.id', 
                '=',
                'student_grades.assessment_id')
                ->where('school_calendar_phase_id', 
                            $school_calendar_phase_id);

        }elseif($school_calendar_id) {
            
            $query->join('assessments', 
                'assessments.id', 
                '=',
                'student_grades.assessment_id')
                ->join('school_calendar_phases',
                    'school_calendar_phases.id',
                    '=',
                    'assessments.school_calendar_phase_id')
                ->where('school_calendar_id', $school_calendar_id);
        }

        return $query;

    }

    /**
     * Resumo anual de notas e faltas do aluno
     * 
     * @param  integer $school_calendar_id       
     * @param  integer $school_calendar_phase_id Opctional. Inclui resumo da fase do ano.
     * 
     * @return array [
     *         'absences_year' => 25,               // Faltas no ano
     *         'absences_year_phase' => 4,           // Faltas nota do bimestre/fase
     *         'best_average_year' => [
     *              '9.7'                           // Melhor nota do ano
     *              'subject' => [
     *                  'id' => 10,
     *                  'name' => 'Matématica'
     *              ]   
     *         ],
     *         'low_average_year' => [
     *             'average' => '5.2',              // Nota mais baixa do ano
     *             'subject' => [...] 
     *         ],   
     *         'best_average_year_phase' => [
     *             'average' => '5.2',              // Melhor nota do bimestre/fase
     *             'subject' => [...] 
     *         ]
     *         'low_average_year_phase' => [...]    // Pior nota do bimestre/fase
     * ]                            
     */
    public function annualSummary($school_calendar_id, $school_calendar_phase_id=0)
    {
         $absences = $this->absencesYear($school_calendar_id)
            ->where('presence', 0)
            ->count();

        // Faltas no ano
        $result['absences_year'] = $absences;

        // Melhor nota no ano
        $data = $this->studentGradesAverage($school_calendar_id)
            ->orderBy('average', 'desc')
            ->first();

        $result['best_average_year'] = [
            'average' => $data ? $data->average : '',
            'subject' => $data ? $data->subject->toArray() : ''
        ];

        // Nota mais baixa do ano
        $data = $this->studentGradesAverage($school_calendar_id)
            ->orderBy('average', 'asc')
            ->first();
        $result['low_average_year'] = [
            'average' => $data ? $data->average : '',
            'subject' => $data ? $data->subject->toArray() : '' 
        ];

        if ($school_calendar_phase_id) {

            $absences = $this->absencesYearPhase($school_calendar_phase_id)
                ->where('presence', 0)
                ->count();

            $result['absences_year_phase'] = $absences;

            $data = $this->studentGradesAverage($school_calendar_id,
                $school_calendar_phase_id)
                ->orderBy('average', 'desc')
                ->first();

            // Nota mais alta da fase do ano
            $result['best_average_year_phase'] = [
                'average' => $data ? $data->average : '',
                'subject' => $data ? $data->subject->toArray() : ''
            ];

            // Nota mais baixa da fase do ano
            $data = $this->studentGradesAverage($school_calendar_id,
                $school_calendar_phase_id)
                ->orderBy('average', 'asc')
                ->first();
            $result['low_average_year_phase'] = [
                'average' => $data ? $data->average : '',
                'subject' => $data ? $data->subject->toArray() : '' 
            ];


        }

        return $result;
    }

    /**
     * Registros de chamada do aluno durante
     * um ano letivo especifico (SchoolCalendar)
     * 
     * @param  int $school_calendar_id
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function absencesYear($school_calendar_id)
    {
        return $this->attendanceRecords()
            ->join('lessons', 'lessons.id', 
                '=' ,
                'attendance_records.lesson_id')
            
            ->join('school_classes', 
                'school_classes.id', 
                '=', 
                'lessons.school_class_id')
            
            ->where('school_classes.school_calendar_id', $school_calendar_id);
    }

    /**
     * Registros de chamada do aluno durante
     * uma fase de ano letivo (SchoolCalendarPhase)
     * 
     * @param  int $school_calendar_id 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function absencesYearPhase($school_calendar_phase_id)
    {
        return $this->attendanceRecords()
            ->join('lessons', 
                'lessons.id', 
                '=' ,
                'attendance_records.lesson_id')
            
            ->join('school_classes', 
                'school_classes.id', 
                '=', 
                'lessons.school_class_id')
            
            ->join('school_calendars', 
                'school_calendars.id', 
                '=', 
                'school_classes.school_calendar_id')

            ->join('school_calendar_phases',
                'school_calendar_phases.school_calendar_id',
                '=',
                'school_calendars.id')

            ->where('school_calendar_phases.id', $school_calendar_phase_id)
            
            ->whereRaw('DATE_FORMAT(lessons.start, "%Y-%m-%d") >= school_calendar_phases.start')

            ->whereRaw('DATE_FORMAT(lessons.end, "%Y-%m-%d") <= school_calendar_phases.end');
    }


    /**
     * Resumo de ausência do aluno em um ano letivo,
     * de uma disciplina
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

        return [
            'percentage_absences_reprove' => AccountConfig::getPercentageAbsencesReprove(),
            'total_lessons_year' =>  Lesson::totalLessonsInYear($school_class_id, $subject_id),
            'total_absences_year' => $total_absences,
        ];

        return $totalAbsences;
    }
}
