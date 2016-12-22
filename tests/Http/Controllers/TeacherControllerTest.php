<?php

namespace Tests\Http\Controllers;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

Use Tests\TestCase;

class TeacherControllerTest extends TestCase
{
    /**
     * Turmas que o professor deu aula
     * 
     * @covers App\Http\Controllers\TeacherController
     *
     * @return void
     */
    public function testSchoolClasses()
    {
    	$teacher = factory(App\Teacher::class)->create();
    	$lessons = factory(App\Lesson::class)->create([
    			'teacher_id' => $teacher->id,
    		]); 

        $this->get("api/teachers/{$teacher->id}");
    }
}
