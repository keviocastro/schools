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
        
        $this->get('api/attendance-records?sort=-id',
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
    	$attendanceRecord = factory(App\AttendanceRecord::class)->create();
    	$attendanceRecord_changed = factory(App\AttendanceRecord::class)->make()->toArray();

        $this->put("api/attendance-records/{$attendanceRecord->id}",
        	$attendanceRecord_changed,
        	$this->getAutHeader())
        	->assertResponseStatus(200)
        	->seeJson($attendanceRecord_changed);
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
     * @covers AttendanceRecordController::store
     *
     * @return void
     */
    public function testStore()
    {
        $attendanceRecord = factory(App\AttendanceRecord::class)->make()->toArray();
        
        $this->post('api/attendance-records',
            $attendanceRecord,
            $this->getAutHeader())
            ->assertResponseStatus(201)
            ->seeJson($attendanceRecord);


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
