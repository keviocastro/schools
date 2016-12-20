<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OwnerResource extends Migration
{
    private $tables = [
        'request_accesses',
        'school_calendars',
        'school_calendar_phases',
        'schools',
        'shifts',
        'subjects',
        'school_classes',
        'school_class_subjects',
        'lessons',
        'people',
        'students',
        'student_responsibles',
        'school_class_student',
        'attendance_records',
        'assessments',
        'student_grades',
        'occurences',
        'levels',
        'account_configs',
        'grades',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function ($table) {
                $table->string('created_by')->nullable();
                $table->string('updated_by')->nullable();
                $table->string('deleted_by')->nullable();
            });
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function ($table) {
                $table->dropColumn(['created_by','updated_by','deleted_by']);
            });
        }
    }
}
