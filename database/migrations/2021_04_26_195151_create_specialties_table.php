<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpecialtiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('specialties', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requested_by')->nullable(true);
            $table->string('name')->unique()->nullable(false);
            $table->enum('status', ['pending', 'active'])->default('pending');
            $table->timestamps();
        });

        Schema::table('specialties', function(Blueprint $table) {
            $table->foreign('requested_by')->references('id')->on('specialists')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('specialties', function(Blueprint $table) {
            $table->dropForeign(['requested_by']);
        });

        Schema::dropIfExists('specialties');
    }
}
