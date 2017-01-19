<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LessonPlans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
        Schema::create('lesson_plan_models', function (Blueprint $table) {
            $table->increments('id');
            $table->json('definition');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('lesson_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('lesson_plan_template_id')->nullable();
            $table->json('content');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('lesson_plan_template_id')->references('id')->on('lesson_plan_models');
        });

        Schema::table('lessons',function (Blueprint $table) {
            $table->unsignedInteger('lesson_plan_id')->nullable();

            $table->foreign('lesson_plan_id')->references('id')->on('lesson_plans');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lessons', function(Blueprint $table){
            $table->dropForeign(['lesson_plan_id']);
            $table->dropColumn('lesson_plan_id');
        });
        Schema::drop('lesson_plans');
        Schema::drop('lesson_plan_models');
    }
}
