<?php

use App\AccountConfig;
use App\Lesson;
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
     * @todo Validar resultados retornados
     * 
     * Teste do parametro: attach=students
     * 
     * attach=students =    Retornar os estudantes da aula.
     *                      Retornar a quantidade total de faltas 
     *                      no ano de cada estudante, na mesma diciplina da aula. 
     * 
     * attach=students.attendanceRecord = Retornar o registro de presença do 
     *                                     estudante para a aula.
     *
     * attach=students.last_occurences = Retornar as ultimas 3 ocorrencias 
     *                                     registradas para o estudante.
     *
     * attach=absenceSummary = Resumo total de faltas do aluno durante o ano
     *
     * @covers LessonController::show
     *
     * @return void
     */
    public function testShowParamAttach()
    {
        // Dados criados:
        // 2 estudantes, sendo o primeiro com 2 faltas 
        // e o segundo com 0 faltas
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
        foreach ($students as $key => $stu) {
            
            factory(App\SchoolClassStudent::class)->create([
                    'student_id' => $stu->id,
                    'school_class_id' => $schoolClass->id
                ]);
            factory(App\StudentResponsible::class)->create([
                    'student_id' => $stu->id
                ]);
            
            $students[$key]->last_occurences = factory(App\Occurence::class)->create([
                    'about_person_id' => $stu->id
                ])->toArray();

            foreach ($lessons as $lesson) {
                $students[$key]->attendance_record = factory(App\AttendanceRecord::class)->create([
                    'student_id' => $stu->id,
                    'lesson_id' => $lesson->id,
                    'presence' => $presence
                ])->toArray();
            }
            $presence = 1;
        }

        $students[0]['absence_summary'] = [
                'percentage_absences_reprove' => AccountConfig::getPercentageAbsencesReprove(),
                'total_lessons_year' =>  Lesson::totalLessonsInYear($schoolClass->id, $subject->id),
                'total_absences_year' => 2,
            ];

        $students[1]['absence_summary'] = [
                'percentage_absences_reprove' => AccountConfig::getPercentageAbsencesReprove(),
                'total_lessons_year' =>  Lesson::totalLessonsInYear($schoolClass->id, $subject->id),
                'total_absences_year' => 0,
            ];

        $students->load('person');
        $studentsOrdered = collect($students->toArray())->sortBy(function($student, $key){
            return  $student['person']['name'];
        });


        $lesson = $lessons[0]->load('schoolClass')->toArray();
        $lesson['students'] = $studentsOrdered->all();
        
        $this->get("api/lessons/{$lesson['id']}".
            "?attach=students,students.attendanceRecord,".
            "students.last_occurences,".
            "students.absenceSummary",
            $this->getAutHeader())
            ->assertResponseStatus(200);
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
