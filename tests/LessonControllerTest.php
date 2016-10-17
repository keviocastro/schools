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
        $schoolClass = factory(App\SchoolClass::class)->create();
        $students = factory(App\Student::class, 10)->create([
                'school_class_id' => function() use ($schoolClass){
                    return $schoolClass->id;
                }
            ]);
        $lesson = factory(App\Lesson::class)->create([
                'school_class_id' => $schoolClass->id
            ]);

        $this->get("api/lessons/$lesson->id?with=students",
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJson($lesson->toArray());
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
                    'lessons' => [
                        'id',
                        'start',
                        'end'   
                    ]
                ]
            ]
        ];

        
        $this->get('api/lessons/per-day',
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJsonStructure($structure);
    }

}
