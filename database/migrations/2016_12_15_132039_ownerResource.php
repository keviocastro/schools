<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OwnerResource extends Migration
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
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });

        Schema::table('school_calendars', function ($table) {
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });
        Schema::table('school_calendar_phases', function ($table) {
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });
        Schema::table('schools', function ($table) {
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });
        Schema::table('shifts', function ($table) {
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });
        Schema::table('subjects', function ($table) {
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });
        Schema::table('school_classes', function ($table) {
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });
        Schema::table('school_class_subjects', function ($table) {
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });
        Schema::table('lessons', function ($table) {
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });

        Schema::table('people', function ($table) {
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });
        Schema::table('students', function ($table) {
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });
        Schema::table('student_responsibles', function ($table) {
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });
        Schema::table('school_class_student', function ($table) {
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });
        Schema::table('attendance_records', function ($table) {
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });
        Schema::table('assessments', function ($table) {
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });
        Schema::table('student_grades', function ($table) {
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });

        Schema::table('occurences', function ($table) {
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });
        Schema::table('levels', function ($table) {
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });

        Schema::table('account_configs', function ($table) {
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });

        Schema::table('grades', function ($table) {
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_accesses', function ($table) {
            $table->dropColumn(['created_by','updated_by','deleted_by']);
        });

        Schema::table('school_calendars', function ($table) {
            $table->dropColumn(['created_by','updated_by','deleted_by']);
        });
        Schema::table('school_calendar_phases', function ($table) {
            $table->dropColumn(['created_by','updated_by','deleted_by']);
        });
        Schema::table('schools', function ($table) {
            $table->dropColumn(['created_by','updated_by','deleted_by']);
        });
        Schema::table('shifts', function ($table) {
            $table->dropColumn(['created_by','updated_by','deleted_by']);
        });
        Schema::table('subjects', function ($table) {
            $table->dropColumn(['created_by','updated_by','deleted_by']);
        });
        Schema::table('school_classes', function ($table) {
            $table->dropColumn(['created_by','updated_by','deleted_by']);
        });
        Schema::table('school_class_subjects', function ($table) {
            $table->dropColumn(['created_by','updated_by','deleted_by']);
        });
        Schema::table('lessons', function ($table) {
            $table->dropColumn(['created_by','updated_by','deleted_by']);
        });

        Schema::table('people', function ($table) {
            $table->dropColumn(['created_by','updated_by','deleted_by']);
        });
        Schema::table('students', function ($table) {
            $table->dropColumn(['created_by','updated_by','deleted_by']);
        });
        Schema::table('student_responsibles', function ($table) {
            $table->dropColumn(['created_by','updated_by','deleted_by']);
        });
        Schema::table('school_class_student', function ($table) {
            $table->dropColumn(['created_by','updated_by','deleted_by']);
        });
        Schema::table('attendance_records', function ($table) {
            $table->dropColumn(['created_by','updated_by','deleted_by']);
        });
        Schema::table('assessments', function ($table) {
            $table->dropColumn(['created_by','updated_by','deleted_by']);
        });
        Schema::table('student_grades', function ($table) {
            $table->dropColumn(['created_by','updated_by','deleted_by']);
        });

        Schema::table('occurences', function ($table) {
            $table->dropColumn(['created_by','updated_by','deleted_by']);
        });
        Schema::table('levels', function ($table) {
            $table->dropColumn(['created_by','updated_by','deleted_by']);
        });

        Schema::table('account_configs', function ($table) {
            $table->dropColumn(['created_by','updated_by','deleted_by']);
        });

        Schema::table('grades', function ($table) {
            $table->dropColumn(['created_by','updated_by','deleted_by']);
        });
    }
}
