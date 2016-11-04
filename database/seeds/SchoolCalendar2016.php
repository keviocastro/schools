<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SchoolCalendar2016 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Calendario escolar com 4 bimesres
        $schoolCalendar = factory(App\SchoolCalendar::class)->create([
            'year' => '2016',
            'start' => '2016-01-20',
            'end' => '2016-12-16',
        ]);
        $schoolCalendarPhase1 = factory(App\SchoolCalendarPhase::class)->create([
            'school_calendar_id' => $schoolCalendar->id,
            'name' => '1º Bimestre',
            'start' => '2016-01-16',
            'end' => '2016-04-15'
        ]);
        $schoolCalendarPhase2 = factory(App\SchoolCalendarPhase::class)->create([
            'school_calendar_id' => $schoolCalendar->id,
            'name' => '2º Bimestre',
            'start' => '2016-04-16',
            'end' => '2016-06-30'
        ]);
        $schoolCalendarPhase3 = factory(App\SchoolCalendarPhase::class)->create([
            'school_calendar_id' => $schoolCalendar->id,
            'name' => '3º Bimestre',
            'start' => '2016-08-01',
            'end' => '2016-09-30'
        ]);
        $schoolCalendarPhase4 = factory(App\SchoolCalendarPhase::class)->create([
            'school_calendar_id' => $schoolCalendar->id,
            'name' => '4º Bimestre',
            'start' => '2016-10-01',
            'end' => '2016-12-16'
        ]);


        // Turma e alunos
        $schoolClass = factory(App\SchoolClass::class)->create();
        $students = factory(App\Student::class, 20)->create([
                'school_class_id' => $schoolClass->id
            ])
            ->each(function($student) use ($schoolClass){
                factory(App\StudentResponsible::class)->create([
                        'student_id' => $student->id 
                    ]);
                factory(App\SchoolClassStudent::class)->create([
                        'student_id' => $student->id,
                        'school_class_id' => $schoolClass->id
                    ]);
                if (rand(0,1)) {
                    factory(App\Occurence::class, 4)->create([
                            'about_person_id' => $student->id
                        ]);
                }
            });


        $start = Carbon::createFromFormat('Y-m-d', $schoolCalendarPhase1->start);
        $end = Carbon::createFromFormat('Y-m-d', $schoolCalendarPhase4->end);

        $subject = factory(App\Subject::class)->create();
        $this->createLessonsBetweenTwoDates(
            $start, 
            $end, 
            7,
            $schoolClass,
            $subject);

        $subject = factory(App\Subject::class)->create();
        $this->createLessonsBetweenTwoDates(
            $start, 
            $end, 
            8,
            $schoolClass,
            $subject);

        $subject = factory(App\Subject::class)->create();
        $this->createLessonsBetweenTwoDates(
            $start, 
            $end, 
            9,
            $schoolClass,
            $subject);

        $subject = factory(App\Subject::class)->create();
        $this->createLessonsBetweenTwoDates(
            $start, 
            $end, 
            10,
            $schoolClass,
            $subject);

        $subject = factory(App\Subject::class)->create();
        $this->createLessonsBetweenTwoDates(
            $start, 
            $end, 
            11,
            $schoolClass,
            $subject);

    }

    public function createLessonsBetweenTwoDates(
        $firstDay, 
        $lastDay, 
        $lessonStartTime,
        $schoolClass,
        $subject
        ){

        $startLesson = clone $firstDay;
        $startLesson->hour = $lessonStartTime;
        $endLesson = clone $startLesson;
        $endLesson->addMinutes(45);

        while ( $startLesson->lt($lastDay) ) {
        	if ($startLesson->dayOfWeek !=  Carbon::SUNDAY && 
        		$startLesson->dayOfWeek != Carbon::SATURDAY) {
        		
	        	factory(App\Lesson::class)->create([
	        			'school_class_id' => $schoolClass->id,
	        			'start' => $startLesson,
	        			'end' => $endLesson,
                        'subject_id' => $subject->id
	        		]);
        	}

        	$startLesson->addDays(1);
        	$endLesson->addDays(1);
        }
    }
}
