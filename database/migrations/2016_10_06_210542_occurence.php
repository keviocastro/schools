<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Occurence extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('levels', function(Blueprint $table){
            $table->increments('id');
            $table->string('name');
            $table->softDeletes();
        });

        Schema::create('occurences', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('level_id')->nulllable();
            $table->string('comment');
            $table->unsignedInteger('owner_person_id');
            $table->unsignedInteger('about_person_id')->nulllable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->foreign('level_id')->references('id')->on('levels');
            $table->foreign('owner_person_id')->references('id')->on('people');
            $table->foreign('about_person_id')->references('id')->on('people');
        });    

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('occurences');
        Schema::drop('levels');
    }
}
