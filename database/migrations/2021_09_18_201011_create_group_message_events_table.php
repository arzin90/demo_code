<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupMessageEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_message_events', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('group_message_id')->nullable();
            $table->boolean('is_read')->nullable()->default(0);
            $table->boolean('is_deleted')->nullable()->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('group_message_id')->references('id')->on('group_messages')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('group_message_events', function(Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['group_message_id']);
        });

        Schema::dropIfExists('group_message_events');
    }
}
