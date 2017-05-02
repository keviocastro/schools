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
        Schema::create('evaluation_sheets', function (Blueprint $table) {
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
        });


        Schema::create('evaluation_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        // Item de avaliação
        Schema::create('evaluation_sheet_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('evaluation_sheet_id');
            $table->unsignedInteger('evaluation_group_id')->nullable();

            $table->foreign('evaluation_sheet_id')
                ->references('id')
                ->on('evaluation_sheets');

            $table->foreign('evaluation_group_id')
                ->references('id')
                ->on('evaluation_groups');
        });


        Schema::create('evaluation_sheet_item_student', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('student_id');

            // Identifier de evaluation_sheets.options
            $table->string('option_identifier');
            $table->timestamps();

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
        Schema::drop('evaluation_sheet_item_student');
        Schema::drop('evaluation_sheet_items');
        Schema::drop('evaluation_groups');
        Schema::drop('evaluation_sheets');
    }
}
