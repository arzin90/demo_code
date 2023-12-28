<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSpecialistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_specialists', function(Blueprint $table) {
            $table->id();
            $table->enum('status', ['pending', 'active', 'blocked', 'for_checking'])->default('active');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('specialist_id');
            $table->string('pseudonym')->nullable();
            $table->boolean('notified')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('specialist_id')->references('id')->on('specialists')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_specialists', function(Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['specialist_id']);
        });

        Schema::dropIfExists('user_specialists');
    }
}
