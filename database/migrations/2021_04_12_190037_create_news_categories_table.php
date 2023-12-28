<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news_categories', function(Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->bigInteger('news_id')->unsigned()->nullable(false);
            $table->bigInteger('category_id')->unsigned()->nullable(false);
            $table->timestamps();
        });

        Schema::table('news_categories', function(Blueprint $table) {
            $table->foreign('news_id')->references('id')->on('news')->cascadeOnDelete();
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('news_categories', function(Blueprint $table) {
            $table->dropForeign(['news_id']);
            $table->dropForeign(['category_id']);
        });

        Schema::dropIfExists('news_categories');
    }
}
