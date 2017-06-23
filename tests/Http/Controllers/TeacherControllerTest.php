<?php

use App\SchoolClass;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class TeacherControllerTest extends TestCase
{
    /**
     * 
     * @covers App\Http\Controllers\TeacherController::schoolClasses
     *
     * @return void
     */
    public function testSchoolClasses(){

        // O professor leciona em 4 turmas para 2 disciplinas diferentes,
        // então devem existir 8 registros de resultado porque para cada turma
        // e disciplina diferente é retornado 1 registro
        $teacher = factory(App\Teacher::class)->create();
        $schoolClasses = factory(App\SchoolClass::class, 4)->create();
        $dataResult = [];
        $subject = factory(App\Subject::class, 2)
            ->create()
            ->each(function($subject) 
                use ($schoolClasses, $teacher, &$dataResult){

                foreach ($schoolClasses as $class) {
                    factory(App\Lesson::class, 6)->create([
                        'school_class_id' => $class->id,
                        'teacher_id' => $teacher->id,
                        'subject_id' => $subject->id,
                    ]);

                    // Load para testar se as relações obtidas através do parametros _with
                    // estão sendo retornadas
                    array_push($dataResult, [
                            'schoolClass' => $class->load('grade', 'shift')->toArray(),
                            'subject' => $subject->toArray()
                        ]);
                }

            });

        $resource = [
                "total" => 8,
                "per_page" => 15,
                "current_page" => 1,
                "last_page" => 1,
                "next_page_url" => null,
                "prev_page_url" => null,
                "from" => 1,
                "to" => 8,
                "data" => $dataResult
            ];

        $this->get("api/teachers/$teacher->id/school-classes".
            "?_sort=school_class_id,subject_id".
            "&_with=schoolClass.grade,schoolClass.shift",
            $this->getAutHeader())
            ->assertStatus(200)
            ->assertJsonFragmentEquals($resource);
    }
}
