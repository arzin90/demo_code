<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEducationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('education', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('specialist_id')->nullable(false);
            $table->string('level')->nullable(false);
            $table->string('institution')->nullable(false);
            $table->string('faculty')->nullable(false);
            $table->string('specialty')->nullable(false);
            $table->date('graduation_at')->nullable(false);
            $table->date('deleted_at')->nullable();
            $table->timestamps();
        });

        Schema::table('education', function(Blueprint $table) {
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
        Schema::table('education', function(Blueprint $table) {
            $table->dropForeign(['specialist_id']);
        });

        Schema::dropIfExists('education');
    }
}
