<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Users extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('people', function(Blueprint $table){
            $table->string('user_id')->nullable(); // id do usuário no serviço de autentificação: exmplo auth0
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('people', function(Blueprint $table)
        {
            $table->dropColumn('user_id');
        });
    }
}
