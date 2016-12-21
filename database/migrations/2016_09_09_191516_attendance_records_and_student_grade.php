
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AttendanceRecordsAndStudentGrade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Registros de presença
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('lesson_id');
            $table->unsignedInteger('student_id');
            // 0 Faltou a aula
            // 1 Estava presente
            // 2 Falta abonada
            $table->tinyInteger('presence');
            $table->string('absence_dismissal');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('lesson_id')->references('id')->on('lessons');
            $table->foreign('student_id')->references('id')->on('students');

            $table->unique(['lesson_id', 'student_id']);
        });

        // Avaliações de uma fase do ano letivo
        Schema::create('assessments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('school_calendar_phase_id');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_calendar_phase_id')
                ->references('id')
                ->on('school_calendar_phases');
        });


        // Notas dos alunos de uma avaliação em uma fase do ano letivo
        Schema::create('student_grades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->float('grade');
            $table->unsignedInteger('student_id');
            $table->unsignedInteger('assessment_id');
            $table->unsignedInteger('subject_id');
            $table->unsignedInteger('school_class_id');
            $table->timestamps();
            $table->softDeletes();

            // Um estudante não pode ter mais de uma nota para a mesma
            // disciplina na mesma avaliação
            $table->unique(['student_id', 'assessment_id', 'subject_id']);

            $table->foreign('subject_id')
                ->references('id')
                ->on('subjects');

            $table->foreign('school_class_id')
                ->references('id')
                ->on('school_classes');

            $table->foreign('assessment_id')
                ->references('id')
                ->on('assessments');
            
            $table->foreign('student_id')
                ->references('id')
                ->on('students');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('attendance_records');
        Schema::drop('student_grades');
        Schema::drop('assessments');
    }
}
