<?php 

use App\Lesson;
use App\SchoolClass;
use App\Subject;
use App\Teacher;
use App\Person;
use Carbon\Carbon;

/**
 * Fábrica para criar aulas e registros relacionados 
 * em grandes quantidades
 */
class LessonsFactory 
{
    /**
     * Cria 1 aula por dia entre um intervalo de datas.
     * 
     * @param  Carbon $firstDay        
     * @param  Carbon $lastDay         
     * @param  int $lessonStartTime 
     * @param  App\SchoolClass $schoolClass     
     * @param  App\Subject $subject    
     *      
     * @return void                  
     */
    public static function createBetweenTwoDates(
        Carbon $firstDay, 
        Carbon $lastDay, 
        int $lessonStartTime,
        \App\SchoolClass $schoolClass,
        \App\Subject $subject,
        \App\Teacher $teacher
        ){

        $startLesson = clone $firstDay;
        $startLesson->hour = $lessonStartTime;
        $endLesson = clone $startLesson;
        $endLesson->addMinutes(45);

        $lessons = [];
        while ( $startLesson->lt($lastDay) ) {
        	if ($startLesson->dayOfWeek !=  Carbon::SUNDAY && 
        		$startLesson->dayOfWeek != Carbon::SATURDAY) {
        		
                array_push($lessons, [
	        			'school_class_id' => $schoolClass->id,
	        			'start' => $startLesson->format('Y-m-d H:i'),
	        			'end' => $endLesson->format('Y-m-d H:i'),
                        'subject_id' => $subject->id,
                        'teacher_id' => $teacher->id
                    ]);
            
            }

            $startLesson->addDays(1);
            $endLesson->addDays(1);
        }

        \App\Lesson::insert($lessons);
    }

    /**
     * Cria um professor com user_id do servico de autentificação.
     * Se ele já já existir, o professor é retornado.
     * 
     * @param  string $user_id ID do usuaŕio no serviço de autentificação
     * @return Teacher
     */
    public static function createTeacherIfNotExists($user_id)
    {
        $teacher = Teacher::findByUserId($user_id);

        if (empty($teacher)) {
            $teacher = factory(Teacher::class)->create([
                'person_id' => factory(Person::class)->create([
                            'user_id' => $user_id
                        ])->id
            ]);
        }

        return $teacher;
    }
}