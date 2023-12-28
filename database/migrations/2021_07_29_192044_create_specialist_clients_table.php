<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpecialistClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('specialist_clients', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('specialist_id');
            $table->enum('status', ['pending', 'active', 'blocked', 'for_checking'])->default('active');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('patronymic_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('b_day')->nullable();
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
        Schema::table('specialist_clients', function(Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['specialist_id']);
        });

        Schema::dropIfExists('specialist_clients');
    }
}
