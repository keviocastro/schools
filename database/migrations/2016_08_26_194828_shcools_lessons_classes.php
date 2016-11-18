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
        // Calendários escolares/anos letivos
        Schema::create('school_calendars', function(Blueprint $table){
            $table->increments('id');
            $table->integer('year');
            $table->date('start');
            $table->date('end');
            // Calculo de média de notas no ano
            // Exemplo: 
            // ( 1 Bimestre * 0,4 + 2 Bimestre * 0,6 ) / 2 = Média do aluno em uma disciplina no ano
            $table->string('average_calculation'); 
            $table->timestamps();
            $table->softDeletes();

        });    

        // Fases avaliativas de um ano letivo (bimestres, semestres, etc.)
        Schema::create('school_calendar_phases', function(Blueprint $table){
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('school_calendar_id');
            $table->date('start');
            $table->date('end');
            // Calculo de média de notas na fase do ano
            // Exmplo:
            // (NOTA 1 + NOTA 2)/2  = Nota do aluno no bimestre em uma disciplina
            $table->string('average_calculation');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_calendar_id')
                ->references('id')->on('school_calendars');
        });

        // Escolas
        Schema::create('schools', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        // Ano estudantil (Jardim I, 1º Ano, etc.)
        Schema::create('grades', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        // Turno da turma (matutino, vespertino, noturno)
        Schema::create('shifts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        // Disciplina (matématica, português, física quântica)
        Schema::create('subjects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        // Turmas (1º Ano A, Jardim I - A, Jardim I - B)
        Schema::create('school_classes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('identifier');
            $table->unsignedInteger('school_id');
            $table->unsignedInteger('school_calendar_id');
            $table->unsignedInteger('grade_id');
            $table->unsignedInteger('shift_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')
                ->references('id')
                ->on('schools');

            $table->foreign('school_calendar_id')
                ->references('id')
                ->on('school_calendars');
            
            $table->foreign('grade_id')
                ->references('id')->on('grades');
            
            $table->foreign('shift_id')
                ->references('id')
                ->on('shifts');

        });


        // Disciplinas existentes para um turma
        Schema::create('school_class_subjects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('school_class_id');
            $table->unsignedInteger('subject_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_class_id')
                ->references('id')->on('school_classes');
            
            $table->foreign('subject_id')
                ->references('id')->on('subjects');
        });

        // Aulas
        Schema::create('lessons', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('school_class_id');
            $table->unsignedInteger('subject_id');
            $table->dateTime('start');
            $table->dateTime('end');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_class_id')->references('id')->on('school_classes');
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
            'lessons',
            'school_class_subjects',
            'school_classes',
            'subjects',
            'shifts',
            'grades',
            'schools',
            'school_calendar_phases',
            'school_calendars',
        ];

        foreach ($tables as $value) {
            Schema::drop($value);
        }
    }
}
