<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TeacherControllerTest extends TestCase
{
    /**
    *
    */
    public function testSchoolClasses(){
        $teacher = factory(App\Teacher::class)->create();
        $subject = factory(App\Subject::class)->create();
        $schoolClasses = factory(App\Lesson::class)->create([
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id
        ]);

        $structure = [
            "total",
            "per_page",
            "current_page",
            "last_page",
            "next_page_url",
            "prev_page_url",
            "from",
            "to",
            "data" => [
                "*" => 
                [
                    "schoolClass"=>
                    [
                        "id",
                        "identifier",
                        "grade_id",
                        "shift_id",
                        "school_calendar_id",
                        "school_id",
                        'grade' => ['id', 'name'],
                        'shift' => ['id', 'name'],
                        'school_calendar' => ['id', 'year']
                    ],
                    "subject"=>
                    [
                        "id",
                        "name"
                    ]
                ]
            ]
        ];

        $this->get("api/teachers/$teacher->id/school-classes".
            "?_with=schoolClass.grade,schoolClass.shift,schoolClass.schoolCalendar",
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJsonStructure($structure);
    }
}
