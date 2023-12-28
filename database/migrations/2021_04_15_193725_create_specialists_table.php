<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpecialistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('specialists', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('status', ['pending', 'active', 'blocked', 'for_checking', 'deleted'])->default('pending');
            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->timestamps();
        });

        Schema::table('specialists', function(Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('specialists', function(Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('specialists');
    }
}
