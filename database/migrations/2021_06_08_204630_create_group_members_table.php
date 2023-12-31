<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_members', function(Blueprint $table) {
            $table->id();
            $table->enum('status', ['pending', 'active', 'blocked', 'deleted'])->default('active');
            $table->unsignedBigInteger('group_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->boolean('is_admin')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_members');
    }
}
