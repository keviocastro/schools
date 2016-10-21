<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class LessonControllerTest extends TestCase
{
    /**
     * @covers LessonController::index
     *
     * @return void
     */
    public function testIndex()
    {
        $lesson = factory(\App\Lesson::class)->create();

    	$this->get('api/lessons?sort=-id',$this->getAutHeader())
    		->assertResponseStatus(200)
    		->seeJson($lesson->toArray());
    }

    /**
     * @covers LessonController::store
     *
     * @return void
     */
    public function testStore()
    {
    	$lesson = factory(App\Lesson::class)->make()->toArray();
        
        $this->post('api/lessons',
        	$lesson,
        	$this->getAutHeader())
        	->assertResponseStatus(201)
        	->seeJson($lesson);
    }

    /**
     * @covers LessonController::show
     *
     * @return void
     */
    public function testShow()
    {
        $lesson = factory(App\Lesson::class)->create();

        $this->get("api/lessons/$lesson->id",
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJson($lesson->toArray());
    }

    /**
     * Teste do parametro: attache=students
     * Deve retornar os estudantes da aula contendo
     * o registro de presença dos estudantes.
     * Deve retornar a quantidade total de faltas no ano
     * de cada estudante, na mesma diciplina da aula. 
     *
     * @covers LessonController::show
     *
     * @return void
     */
    public function testShowParamAttachStudents()
    {
        // Dados criados:
        // 2 estudantes, sendo o primeiro com 0 faltas 
        // e o segundo com 2 faltas
        $schoolClass = factory(App\SchoolClass::class)->create();
        $subject = factory(App\Subject::class)->create();
        $lessons = factory(App\Lesson::class, 2)->create([
                'school_class_id' => $schoolClass->id,
                'subject_id' => $subject->id
            ]);
        $students = factory(App\Student::class, 2)->create([
                'school_class_id' => function() use ($schoolClass){
                    return $schoolClass->id;
                }
            ]);
        $presence = 0;
        foreach ($students as $stu) {
            
            factory(App\SchoolClassStudent::class)->create([
                    'student_id' => $stu->id,
                    'school_class_id' => $schoolClass->id
                ]);
            foreach ($lessons as $lesson) {
                factory(App\AttendanceRecord::class)->create([
                    'student_id' => $stu->id,
                    'lesson_id' => $lesson->id,
                    'presence' => $presence
                ]);
            }
            $presence = 1;
        }

        $students[0]->totalAbsences = 2;
        $students[0]->totalAbsences = 0;

        $this->get("api/lessons/{$lessons[0]->id}?attach=students",
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJson($students->toArray());
    }
    /**
     * @covers LessonController::update
     *
     * @return void
     */
    public function testUpdate()
    {
        $lesson = factory(App\Lesson::class)->create();
        $lesson_changed = factory(App\Lesson::class)->make()->toArray();
        
        $this->put("api/lessons/{$lesson->id}",
            $lesson_changed,
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJson($lesson_changed);
    }

    /**
     * @covers LessonController::destroy
     * 
     * @return void
     */
    public function testDestroy()
    {
        $lesson = factory(\App\Lesson::class)->create();

        $this->delete("api/lessons/{$lesson->id}",
            [],
            $this->getAutHeader())
            ->assertResponseStatus(204)
            ->seeIsSoftDeletedInDatabase('lessons', ['id' => $lesson->id]);
    }

    /**
     * @covers LessonController::indexLesson
     * 
     * @return void
     */
    public function testListPerDay()
    {

        $lessons = factory(App\Lesson::class, 'Next15Days', 5)->create();

        $structure = [
            'total',
            'per_page',
            'current_page',
            'last_page',
            'next_page_url',
            'prev_page_url',
            'from',
            'to',
            'data' => [
                [
                    'day',
                    'lessons' => []
                ]
            ]
        ];

        
        $this->get('api/lessons/per-day?with=schoolClass.grade',
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJsonStructure($structure);
    }

}
