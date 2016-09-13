<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Students extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Informações básica de pessoas
        Schema::create('people', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->date('birthday')->nulllable();
            $table->string('gender');
            $table->string('place_of_birth');
            $table->string('more');
            $table->timestamps();
        });


        // Estudantes
        Schema::create('students', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('person_id');
            $table->unsignedInteger('school_class_id');
            $table->timestamps();

            $table->foreign('school_class_id')->references('id')->on('school_classes');
            $table->foreign('person_id')->references('id')->on('people');

        });

        // Responsáveis pelo estudante
        Schema::create('student_responsible', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('student_id');
            $table->unsignedInteger('person_id');

            $table->foreign('student_id')->references('id')->on('students');
            $table->foreign('person_id')->references('id')->on('people');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('student_responsible');
        Schema::drop('students');
        Schema::drop('people');
    }
}
