<?php
namespace Tests\Http\Controllers;

use App\AccountConfig;
use App\AttendanceRecord;
use App\Lesson;
use App\Occurence;
use App\SchoolClass;
use App\SchoolClassStudent;
use App\Student;
use App\StudentResponsible;
use App\Subject;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LessonControllerTest extends TestCase
{
    /**
     * @covers App\Http\Controllers\LessonController::index
     *
     * @return void
     */
    public function testIndex()
    {
        $lesson = factory(Lesson::class)->create();
        // para testar o parametro _with
        $lesson->load('lessonPlan', 
            'schoolClass.grade',
            'schoolClass.shift',
            'subject');

    	$this->get('api/lessons?_sort=-id'.
            '&_with=lessonPlan,schoolClass.grade,schoolClass.shift,subject',
            $this->getAutHeader())
    		->assertResponseStatus(200)
    		->seeJson($lesson->toArray());
    }

    /**
     * @covers App\Http\Controllers\LessonController::store
     *
     * @return void
     */
    public function testStore()
    {
    	$lesson = factory(Lesson::class)->make()->toArray();
        
        $this->post('api/lessons',
        	$lesson,
        	$this->getAutHeader())
        	->assertResponseStatus(201)
        	->seeJson($lesson);
    }

    /**
     * @covers App\Http\Controllers\LessonController::show
     *
     * @return void
     */
    public function testShow()
    {
        $lesson = factory(Lesson::class)->create();

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
     * @covers App\Http\Controllers\LessonController::show
     *
     * @return void
     */
    public function testShowParamAttach()
    {
        // A Lesson e os dados de falta do estudante são
        // são gerados pelo seeder SchoolCalendar2016 
        $this->selectDatabaseTest();

        $url = "api/lessons/1?attach=students.attendanceRecord,".
            "students.last_occurences,".
            "students.absenceSummary"; 

        $this->get($url, $this->getAutHeader())
            ->assertResponseStatus(200);
            
        $response = $this->getResponseContent("GET", $url);

        
        // Test param students.absenceSummary
        $student = collect($response['lesson']['students'])->where('id', 1)
            ->first(); 
        $this->assertEquals([
                'percentage_absences_reprove' => 25,
                'total_lessons_year' => 240,
                'total_absences_year' => 15
            ],
            $student['absence_summary']);

        // Test param students.last_occurences
        $ocurrences = Student::find(1)
            ->occurences()
            ->with('level')
            ->orderBy('updated_at', 'desc')
            ->take(2)
            ->get()
            ->toArray();

        $this->assertEquals($ocurrences, $student['last_occurences']);

        // Test param students.attendanceRecord
        $this->assertEquals([
                'lesson_id' => 1,
                'student_id' => 1,
                'absence_dismissal' => '',
                'presence' => 0, // 0 que foi definido pelo seeder
            ],
            collect($student['attendance_record'])->except('id')->toArray() 
        );
    }
    /**
     * @covers App\Http\Controllers\LessonController::update
     *
     * @return void
     */
    public function testUpdate()
    {
        $lesson = factory(Lesson::class)->create();
        $lesson_changed = factory(Lesson::class)->make()->toArray();
        
        $this->put("api/lessons/{$lesson->id}",
            $lesson_changed,
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJson($lesson_changed);
    }

    /**
     * @covers App\Http\Controllers\LessonController::destroy
     * 
     * @return void
     */
    public function testDestroy()
    {
        $lesson = factory(Lesson::class)->create();

        $this->delete("api/lessons/{$lesson->id}",
            [],
            $this->getAutHeader())
            ->assertResponseStatus(204)
            ->seeIsSoftDeletedInDatabase('lessons', ['id' => $lesson->id]);
    }

    /**
     * @covers App\Http\Controllers\LessonController::listPerDay
     * 
     * @return void
     */
    public function testListPerDay()
    {

        // Cria lessons para 3 dias
        $start = Carbon::now();
        $dateLesson = clone $start;
        $end = clone $start;
        $end->addDays(2);
        $result = [];
        $teacher = factory(\App\Teacher::class)->create();

        // Remove existentes para verificar se vai retornar exatamente as 
        // que foram criadas

        Lesson::
            where(DB::raw('DATE_FORMAT(start, "%Y-%m-%d")'), '>=', $start->format('Y-m-d'))
            ->where(DB::raw('DATE_FORMAT(end, "%Y-%m-%d")'), '<=', $end->format('Y-m-d'))
            ->delete();


        $i = 0;
        while ( $dateLesson->lte($end) ) {
            $result[$i]['day'] = $dateLesson->format('Y-m-d');
            // 4 Aulas por dia, sendo 2 para o professor "user_id_role_teacher_1"
            // e 2 para um professores aleatórios
            $lessons = factory(Lesson::class, 2)->create([
                    'start' => $dateLesson->format('Y-m-d H:i:s'),
                    'end' => $dateLesson->format('Y-m-d H:i:s'),
                    'teacher_id' => $teacher->id
                ]);

            // Não deve ser exibido nos resultados porque será aplicado o filtro por professor
            $random_lessons = factory(Lesson::class, 2)->create([
                    'start' => $dateLesson->format('Y-m-d H:i:s'),
                    'end' => $dateLesson->format('Y-m-d H:i:s')
                ]);

            $lessons->load('schoolClass.grade', 
                'schoolClass.shift',
                'schoolClass.students',
                'subject');

            // Attributo "day" e "user_id" que a api retorna mas não existe na base
            $lessons[0]->day = $dateLesson->format('Y-m-d');
            $lessons[1]->day = $dateLesson->format('Y-m-d');
            
            $result[$i]['lessons'] = $lessons->toArray();
            $i++; 
            $dateLesson->addDays(1);
        }

        $jsonResult = [
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
            'data' => $result
        ];

        $this->get("api/lessons/per-day".
            "?start={$start->format('Y-m-d')}".
            "&end={$end->format('Y-m-d')}".
            "&user_id=".$teacher->person->user_id.
            "&_with=schoolClass.grade".
                ",schoolClass.shift".
                ",schoolClass.students".
                ",subject",
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJsonEquals($jsonResult);
    }

}
