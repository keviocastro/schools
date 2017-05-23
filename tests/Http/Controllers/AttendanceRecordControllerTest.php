<?php
namespace Tests\Http\Controllers;

use App\AttendanceRecord;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class AttendanceRecordControllerTest extends TestCase
{
    /**
     * @covers App\Http\Controllers\AttendanceRecordController::index
     *
     * @return void
     */
    public function testIndex()
    {
        $attendanceRecord = factory(AttendanceRecord::class)->create();
        
        $this->get('api/attendance-records?_sort=-id',
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($attendanceRecord->toArray());
    }

    /**
     * @covers App\Http\Controllers\AttendanceRecordController::update
     *
     * @return void
     */
    public function testUpdate()
    {
    	$attendanceRecord = factory(AttendanceRecord::class)->create([
                'presence' => 1
            ]);

        $this->put("api/attendance-records/{$attendanceRecord->id}",
        	['presence' => 0],
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson(['presence' => 0]);


        // Não é permitido alterar o estudante nem a aula.
        $newAttendanceRecord = factory(AttendanceRecord::class)->create();
        $this->put("api/attendance-records/{$attendanceRecord->id}",
            [
                'student_id' => $newAttendanceRecord->student_id,
                'lesson_id' => $newAttendanceRecord->lesson_id
            ],
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJson([
                    'student_id' => $attendanceRecord->student_id,
                    'lesson_id' => $attendanceRecord->lesson_id
                ]);
    }

    /**
     * Abonar falta do aluno
     *
     * @covers App\Http\Controllers\AttendanceRecordController::store
     *
     * @return void
     */
    public function testUpdateAbsenceDismissal()
    {
        $attendanceRecord = factory(AttendanceRecord::class)->create([
            'presence' => 0,
        ])->toArray();
        $attendanceRecord['presence'] = 2;
        $attendanceRecord['absence_dismissal'] = 'O aluno apresentou atestado médico.';

        $this->put("api/attendance-records/{$attendanceRecord['id']}",
            $attendanceRecord,
            $this->getAutHeader())
            ->assertResponseStatus(200)
            ->seeJsonEquals(['attendance_record' => $attendanceRecord]);
    }

    /**
     * @covers App\Http\Controllers\AttendanceRecordController::show
     *
     * @return void
     */
    public function testShow()
    {
    	$attendanceRecord = factory(AttendanceRecord::class)->create()->toArray();
        $this->get("api/attendance-records/{$attendanceRecord['id']}",
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($attendanceRecord);
    }

    /**
     * Deve permitir registrar um array de registros.
     * 
     * @covers App\Http\Controllers\AttendanceRecordController::store
     *
     * @return void
     */
    public function testStore()
    {
        $attendanceRecord = factory(AttendanceRecord::class)->make()->toArray();
        
        // Criando 1 registro
        $this->post('api/attendance-records',
            $attendanceRecord,
            $this->getAutHeader())
            ->assertResponseStatus(201)
            ->seeJson($attendanceRecord);

        $attendanceRecords = factory(AttendanceRecord::class, 2)->make()->toArray();
        
        // Criando multiplos registros
        $this->post('api/attendance-records',
            $attendanceRecords,
            $this->getAutHeader())
            ->assertResponseStatus(201)
            ->seeJson($attendanceRecords[0])
            ->seeJson($attendanceRecords[1]);

        // Já existe registro de ocorrência para o aluno.
        // O registro será atualizado.
    	$attendanceRecord = factory(AttendanceRecord::class)->create();
        $attendanceRecord->appliedAction = 'updated';
        $attendanceRecord->presence = ($attendanceRecord->presence == 1) ? 0 : 1;
        $this->post('api/attendance-records',
            [
                'student_id' => $attendanceRecord->student_id,
                'lesson_id' => $attendanceRecord->lesson_id,
                'presence' => $attendanceRecord->presence,
            ],
            $this->getAutHeader())
            ->assertResponseStatus(201)
            ->seeJson($attendanceRecord->toArray());
    }

    /**
     * @covers App\Http\Controllers\AttendanceRecordController::destroy
     * 
     * @return void
     */
    public function testDestroy()
    {
        $attendanceRecord = factory(AttendanceRecord::class)->create();

        $this->delete("api/attendance-records/{$attendanceRecord->id}",
            [],
            $this->getAutHeader())
            ->assertResponseStatus(204)
            ->seeIsSoftDeletedInDatabase('attendance_records', ['id' => $attendanceRecord->id]);
    }
}
