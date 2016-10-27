<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class Lessons2016 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $start = Carbon::create(2016, 1, 10, 8);
        $end = Carbon::create(2016, 1, 10, 8, 45);
        $last = Carbon::create(2016, 12, 10, 8, 45);

        $schoolClass = factory(App\SchoolClass::class)->create();
        $students = factory(App\Student::class, 35)->create([
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

        $subject = factory(App\Subject::class)->create();
        while ( $start->lt($last) ) {
        	if ($start->dayOfWeek !=  Carbon::SUNDAY && 
        		$start->dayOfWeek != Carbon::SATURDAY) {
        		
	        	factory(App\Lesson::class)->create([
	        			'school_class_id' => $schoolClass->id,
	        			'start' => $start,
	        			'end' => $end,
                        'subject_id' => $subject->id
	        		]);
        	}

        	$start->addDays(1);
        	$end->addDays(1);
        }
    }
}
