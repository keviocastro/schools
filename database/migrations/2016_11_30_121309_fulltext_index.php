<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FulltextIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE schools ADD FULLTEXT INDEX search(name)');
        DB::statement('ALTER TABLE school_classes ADD FULLTEXT INDEX search(identifier)');
        DB::statement('ALTER TABLE assessments ADD FULLTEXT INDEX search(name)');
        DB::statement('ALTER TABLE occurences ADD FULLTEXT INDEX search(comment)');
        DB::statement('ALTER TABLE subjects ADD FULLTEXT INDEX search(name)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schools', function($table){
            $table->dropIndex('search');
        });
        Schema::table('school_classes', function($table){
            $table->dropIndex('search');
        });
        Schema::table('assessments', function($table){
            $table->dropIndex('search');
        });
        Schema::table('occurences', function($table){
            $table->dropIndex('search');
        });
        Schema::table('subjects', function($table){
            $table->dropIndex('search');
        });
    }
}
