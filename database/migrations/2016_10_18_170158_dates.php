<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Dates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('days', function(Blueprint $table){
            $table->increments('id');
            $table->date('day');
            $table->timestamps();
        });

        Schema::table('lessons', function(Blueprint $table){
            $table->unsignedInteger('day_id')->nullable();

            $table->foreign('day_id')
                ->references('id')->on('days');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('days');
    }
}
