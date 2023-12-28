<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgramCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('program_comments', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->text('message')->nullable();
            $table->boolean('is_deleted')->nullable(false)->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('program_id')->references('id')->on('programs')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('program_comments', function(Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['program_id']);
        });

        Schema::dropIfExists('program_comments');
    }
}
