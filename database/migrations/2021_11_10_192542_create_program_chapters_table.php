<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgramChaptersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('program_chapters', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->unsignedBigInteger('chapter_id')->nullable();
            $table->timestamps();

            $table->foreign('program_id')->references('id')->on('programs')->nullOnDelete();
            $table->foreign('chapter_id')->references('id')->on('chapters')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('program_chapters', function(Blueprint $table) {
            $table->dropForeign(['program_id']);
            $table->dropForeign(['chapter_id']);
        });

        Schema::dropIfExists('program_chapters');
    }
}
