<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgramProgramCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('programs', function(Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id']);
        });

        Schema::create('program_program_categories', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('program_category_id');
            $table->timestamps();

            $table->foreign('program_id')->references('id')->on('programs')->cascadeOnDelete();
            $table->foreign('program_category_id')->references('id')->on('program_categories')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('programs', function(Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('location_id');
            $table->foreign('category_id')->references('id')->on('program_categories')->nullOnDelete();
        });

        Schema::table('program_program_categories', function(Blueprint $table) {
            $table->dropForeign(['program_id']);
            $table->dropForeign(['program_category_id']);
        });

        Schema::dropIfExists('program_program_categories');
    }
}
