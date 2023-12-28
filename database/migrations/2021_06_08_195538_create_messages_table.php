<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from')->nullable();
            $table->unsignedBigInteger('to')->nullable();
            $table->text('message')->nullable();
            $table->unsignedBigInteger('replay')->nullable();
            $table->boolean('from_read')->default(0);
            $table->boolean('to_read')->default(0);
            $table->boolean('from_deleted')->default(0);
            $table->boolean('to_deleted')->default(0);
            $table->timestamps();

            $table->foreign('from')->references('id')->on('users')->nullOnDelete();
            $table->foreign('to')->references('id')->on('users')->nullOnDelete();
            $table->foreign('replay')->references('id')->on('users')->nullOnDelete();
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
            $table->dropForeign(['from']);
            $table->dropForeign(['to']);
        });

        Schema::dropIfExists('messages');
    }
}
