<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveForeignKeyFromMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('messages', function(Blueprint $table) {
            $table->dropForeign(['from']);
            $table->dropForeign(['to']);
            $table->dropForeign(['replay']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('messages', function(Blueprint $table) {
            $table->foreign('from')->references('id')->on('users')->nullOnDelete();
            $table->foreign('to')->references('id')->on('users')->nullOnDelete();
            $table->foreign('replay')->references('id')->on('users')->nullOnDelete();
        });
    }
}
