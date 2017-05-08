<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Epic "Avaliações descritivas e turmas multidisciplinares"
 * 
 * @see https://github.com/keviocastro/daily-ui/issues/2
 * 
 */
class DescriptiveAssessments extends Migration
{
    /**
     * 
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Ficha de avaliação
        Schema::create('progress_sheets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');

            /**
             * options:
             * Para armazenar um estrutura fixa de respostas por item de avaliação
             *
             * Exemplo: [
             *      {"identifier":"I", "label": "Irregular"},
             *      {"identifier":"R", "label": "Regular"},
             *      {"identifier":"B", "label": "Bom"},
             *      {"identifier":"O", "label": "Ótimo"},
             *  ]
             */
            $table->json('options');
            $table->timestamps();

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->softDeletes();
        });

        // Item de avaliação
        Schema::create('progress_sheet_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('progress_sheet_id');

            $table->foreign('progress_sheet_id')
                ->references('id')
                ->on('progress_sheets');

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });


        Schema::create('student_progress_sheets', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('student_id');
            $table->unsignedInteger('progress_sheet_item_id');
            $table->unsignedInteger('school_calendar_phase_id');

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->softDeletes();

            // Identifier de progress_sheets.options
            $table->string('option_identifier');
            $table->timestamps();

            $table->foreign('student_id')
                ->references('id')
                ->on('students');
            $table->foreign('progress_sheet_item_id')
                ->references('id')
                ->on('progress_sheet_items');
            $table->foreign('school_calendar_phase_id')
                ->references('id')
                ->on('school_calendar_phases');
        });

        Schema::table('school_classes', function(Blueprint $table){
            // Valores possíveis:
            // grade_per_phase
            // progress_sheet_per_phase
            $table->string('evaluation_type')->default('grade_per_phase');
            $table->unsignedInteger('progress_sheet_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('student_progress_sheet');
        Schema::drop('progress_sheet_items');
        Schema::drop('progress_sheets');
        Schema::table('school_classes', function(Blueprint $table) {
            $table->dropColumn('evaluation_type');
            $table->dropColumn('progress_sheet_id');
        });
    }
}