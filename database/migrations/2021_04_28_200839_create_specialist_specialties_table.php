<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpecialistSpecialtiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('specialist_specialties', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('specialist_id')->nullable(false);
            $table->unsignedBigInteger('speciality_id')->nullable(false);
            $table->timestamps();
        });

        Schema::table('specialist_specialties', function(Blueprint $table) {
            $table->foreign('specialist_id')->references('id')->on('specialists')->cascadeOnDelete();
            $table->foreign('speciality_id')->references('id')->on('specialties')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('specialist_specialties', function(Blueprint $table) {
            $table->dropForeign(['specialist_id']);
            $table->dropForeign(['speciality_id']);
        });

        Schema::dropIfExists('specialist_specialties');
    }
}
