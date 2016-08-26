<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ShcoolsLessonsClasses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::create('schools', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('grades', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('shifts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('classes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('identifier');
            $table->unsignedInteger('grade_id');
            $table->unsignedInteger('shift_id');
            $table->timestamps();

            $table->foreign('grade_id')->references('id')->on('grades');
            $table->foreign('shift_id')->references('id')->on('shifts');

        });


        Schema::create('lessons', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('class_id');
            $table->unsignedInteger('subject_id');
            $table->dateTime('start');
            $table->dateTime('end');
            $table->timestamps();

            $table->foreign('class_id')->references('id')->on('classes');
            $table->foreign('subject_id')->references('id')->on('subjects');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = [
            'schools',
            'lessons',
            'classes',
            'grades',
            'shifts',
            'subjects',
        ];

        foreach ($tables as $value) {
            Schema::drop($value);
        }
    }
}
