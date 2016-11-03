<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class AttendanceRecordControllerTest extends TestCase
{
    /**
     * @covers AttendanceRecordController::index
     *
     * @return void
     */
    public function testIndex()
    {
        $attendanceRecord = factory(App\AttendanceRecord::class)->create();
        
        $this->get('api/attendance-records?_sort=-id',
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($attendanceRecord->toArray());
    }

    /**
     * @covers AttendanceRecordController::update
     *
     * @return void
     */
    public function testUpdate()
    {
    	$attendanceRecord = factory(App\AttendanceRecord::class)->create([
                'presence' => 1
            ]);

        $this->put("api/attendance-records/{$attendanceRecord->id}",
        	['presence' => 0],
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson(['presence' => 0]);


        // Não é permitido alterar o estudante nem a aula.
        $newAttendanceRecord = factory(App\AttendanceRecord::class)->create();
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
     * @covers AttendanceRecordController::show
     *
     * @return void
     */
    public function testShow()
    {
    	$attendanceRecord = factory(App\AttendanceRecord::class)->create()->toArray();
        $this->get("api/attendance-records/{$attendanceRecord['id']}",
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($attendanceRecord);
    }

    /**
     * Deve permitir registrar um array de registros.
     * 
     * @covers AttendanceRecordController::store
     *
     * @return void
     */
    public function testStore()
    {
        $attendanceRecord = factory(App\AttendanceRecord::class)->make()->toArray();
        
        // Criando 1 registro
        $this->post('api/attendance-records',
            $attendanceRecord,
            $this->getAutHeader())
            ->assertResponseStatus(201)
            ->seeJson($attendanceRecord);

        $attendanceRecords = factory(App\AttendanceRecord::class, 2)->make()->toArray();
        
        // Criando multiplos registros
        $this->post('api/attendance-records',
            $attendanceRecords,
            $this->getAutHeader())
            ->assertResponseStatus(201)
            ->seeJson($attendanceRecords[0])
            ->seeJson($attendanceRecords[1]);

        // The record of the student to the lesson already exists.
    	$attendanceRecord = factory(App\AttendanceRecord::class)->create();
        $this->post('api/attendance-records',
            [
                'student_id' => $attendanceRecord->student_id, 
                'lesson_id' => $attendanceRecord->lesson_id, 
                'presence' => 1,
            ],
            $this->getAutHeader())
            ->assertResponseStatus(409);
    }

    /**
     * @covers AttendanceRecordController::destroy
     * 
     * @return void
     */
    public function testDestroy()
    {
        $attendanceRecord = factory(\App\AttendanceRecord::class)->create();

        $this->delete("api/attendance-records/{$attendanceRecord->id}",
            [],
            $this->getAutHeader())
            ->assertResponseStatus(204)
            ->seeIsSoftDeletedInDatabase('attendance_records', ['id' => $attendanceRecord->id]);
    }
}
