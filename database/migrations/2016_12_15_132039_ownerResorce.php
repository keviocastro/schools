<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OwnerResorce extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_accesses', function ($table) {
            $table->string('created_by');
            $table->string('updated_by');
            $table->string('deleted_by');
        });

        Schema::table('school_calendars', function ($table) {
            $table->string('created_by');
            $table->string('updated_by');
            $table->string('deleted_by');
        });
        Schema::table('school_calendar_phases', function ($table) {
            $table->string('created_by');
            $table->string('updated_by');
            $table->string('deleted_by');
        });
        Schema::table('schools', function ($table) {
            $table->string('created_by');
            $table->string('updated_by');
            $table->string('deleted_by');
        });
        Schema::table('shifts', function ($table) {
            $table->string('created_by');
            $table->string('updated_by');
            $table->string('deleted_by');
        });
        Schema::table('subjects', function ($table) {
            $table->string('created_by');
            $table->string('updated_by');
            $table->string('deleted_by');
        });
        Schema::table('school_classes', function ($table) {
            $table->string('created_by');
            $table->string('updated_by');
            $table->string('deleted_by');
        });
        Schema::table('school_class_subjects', function ($table) {
            $table->string('created_by');
            $table->string('updated_by');
            $table->string('deleted_by');
        });
        Schema::table('lessons', function ($table) {
            $table->string('created_by');
            $table->string('updated_by');
            $table->string('deleted_by');
        });

        Schema::table('people', function ($table) {
            $table->string('created_by');
            $table->string('updated_by');
            $table->string('deleted_by');
        });
        Schema::table('students', function ($table) {
            $table->string('created_by');
            $table->string('updated_by');
            $table->string('deleted_by');
        });
        Schema::table('student_responsibles', function ($table) {
            $table->string('created_by');
            $table->string('updated_by');
            $table->string('deleted_by');
        });
        Schema::table('school_class_student', function ($table) {
            $table->string('created_by');
            $table->string('updated_by');
            $table->string('deleted_by');
        });
        Schema::table('attendance_records', function ($table) {
            $table->string('created_by');
            $table->string('updated_by');
            $table->string('deleted_by');
        });
        Schema::table('assessments', function ($table) {
            $table->string('created_by');
            $table->string('updated_by');
            $table->string('deleted_by');
        });
        Schema::table('student_grades', function ($table) {
            $table->string('created_by');
            $table->string('updated_by');
            $table->string('deleted_by');
        });

        Schema::table('occurences', function ($table) {
            $table->string('created_by');
            $table->string('updated_by');
            $table->string('deleted_by');
        });
        Schema::table('levels', function ($table) {
            $table->string('created_by');
            $table->string('updated_by');
            $table->string('deleted_by');
        });

        Schema::table('account_configs', function ($table) {
            $table->string('created_by');
            $table->string('updated_by');
            $table->string('deleted_by');
        });

        Schema::table('grades', function ($table) {
            $table->string('created_by');
            $table->string('updated_by');
            $table->string('deleted_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
