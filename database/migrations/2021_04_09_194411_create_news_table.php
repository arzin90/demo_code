<?php

use App\Models\News;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news', function(Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->enum('status_id', array_keys(News::getStatus()))->default(News::STATUS_PENDING)->nullable(false);
            $table->string('title')->nullable(false);
            $table->integer('location_id')->unsigned()->nullable();
            $table->text('short_description')->nullable(false);
            $table->longText('description')->nullable(false);
            $table->integer('view_count')->unsigned()->default(0);
            $table->timestamps();
        });

        Schema::table('news', function(Blueprint $table) {
            $table->foreign('location_id')->references('id')->on('locations')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('news', function(Blueprint $table) {
            $table->dropForeign(['location_id']);
        });

        Schema::dropIfExists('news');
    }
}
