<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DescriptiveAssessments extends Migration
{
    /**
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
             * options_per_item:
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

        // Item de avaliação
        Schema::create('evaluation_sheet_itens', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('evaluation_sheet_id');
            $table->string('name');

            $table->foreign('evaluation_sheet_id')
                ->references('id')
                ->on('evaluation_sheets');
        });

        Schema::create('evaluation_sheet_iten_student', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('student_id');

            // Identifier de evaluation_sheets.options
            $table->string('option_identifier');
            $table->timestamps();
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
