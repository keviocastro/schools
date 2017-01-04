<?php

use Illuminate\Database\Seeder;

class FakeDataForTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Lesson::class, 20)->create();
        factory(App\AttendanceRecord::class, 20)->create();
        factory(App\SchoolCalendarPhase::class, 20)->create();
        factory(App\StudentGrade::class, 20)->create();
        factory(App\Assessment::class, 20)->create();
        factory(App\Level::class, 3)->create();
        factory(App\Occurence::class, 20)->create();
        factory(App\Teacher::class, 20)->create();
    }
}
