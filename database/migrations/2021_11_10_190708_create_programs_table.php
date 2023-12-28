<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programs', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('specialist_id')->nullable(true);
            $table->enum('status', ['pending', 'active', 'for_checking', 'inactive'])->default('pending');
            $table->unsignedInteger('location_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('media_id')->nullable();
            $table->string('name');
            $table->double('price');
            $table->double('sale_price')->nullable();
            $table->text('link')->nullable();
            $table->unsignedInteger('member_count')->nullable();
            $table->mediumText('description')->nullable();
            $table->string('time_zone')->nullable();
            $table->timestamps();

            $table->foreign('location_id')->references('id')->on('locations')->nullOnDelete();
            $table->foreign('specialist_id')->references('id')->on('specialists')->nullOnDelete();
            $table->foreign('media_id')->references('id')->on('media')->nullOnDelete();
            $table->foreign('category_id')->references('id')->on('program_categories')->nullOnDelete();
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
            $table->dropForeign(['specialist_id']);
            $table->dropForeign(['location_id']);
            $table->dropForeign(['media_id']);
            $table->dropForeign(['category_id']);
        });

        Schema::dropIfExists('programs');
    }
}
