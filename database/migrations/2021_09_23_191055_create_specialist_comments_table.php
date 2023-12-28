<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpecialistCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('specialist_comments', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('specialist_id')->nullable();
            $table->text('message')->nullable();
            $table->boolean('is_deleted')->nullable(false)->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('specialist_id')->references('id')->on('specialists')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('specialist_comments', function(Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['specialist_id']);
        });

        Schema::dropIfExists('specialist_comments');
    }
}
