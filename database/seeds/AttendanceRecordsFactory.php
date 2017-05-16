<?php

use App\AttendanceRecord;
use App\SchoolCalendarPhase;

/**
 * Fábrica para criar registro de chamadas em grandes quantidades
 */
class AttendanceRecordsFactory 
{
	/**
     * Cria registro de presença para todas as aulas 
     * da fase do ano letivo $schoolCalendarPhase
     * 
     * @param  SchoolCalendarPhase $schoolCalendarPhase  
     * @param  int                 $totalAbsences        Opcional
     * @param  int                 $student_id           Opcional. Atribui total de faltas ($totalAbsences) para esse aluno.     
     * @param  int                 $school_class_id      Turma das aulas que serão registradas as presenças
     *    
     * @return void                                   
     */
    public static function create(
        SchoolCalendarPhase $schoolCalendarPhase,
        int $totalAbsences,
        int $student_id=null,
        int $school_class_id
        ){

        $lessons = $schoolCalendarPhase->lessons()
            ->where('lessons.school_class_id', $school_class_id)
            ->get();

        $attendanceRecords = [];
        $total_absences_student_id = 0;

        foreach ($lessons as $lesson) {

            foreach ($lesson->students() as $key => $student) {
                
                if ($student_id == $student->id &&
                        $total_absences_student_id < $totalAbsences && 
                        $lesson->subject_id == 1 
                    ){
                    
                    $presence = 0;
                    $total_absences_student_id++;
                }else{
                    $presence = ($key <= $totalAbsences-1) ? 0 : 1;
                }

                array_push($attendanceRecords, [
                        'lesson_id' => $lesson->id,
                        'student_id' => $student->id,
                        'presence' => $presence
                    ]);
            }

        }

        App\AttendanceRecord::insert($attendanceRecords);
    }
}