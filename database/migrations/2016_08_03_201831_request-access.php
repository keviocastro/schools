<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RequestAccess extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Solicitações de acesso ao sistema
        Schema::create('request_accesses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('auth0_user_id');
            // 0 - pending
            // 1 - denied
            // 2 - aprroved
            $table->tinyInteger('status')->default(0);
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
        Schema::drop('request_accesses');
    }
}
