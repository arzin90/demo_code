<?php

use App\Models\UserStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('status_id')->nullable()->default(UserStatus::PENDING);
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('patronymic_name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('phone')->unique()->nullable();
            $table->string('password')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('b_day')->nullable();
            $table->text('content')->nullable();
            $table->string('url')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::table('users', function(Blueprint $table) {
            $table->foreign('status_id')->references('id')->on('user_status')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->dropForeign(['status_id']);
        });

        Schema::dropIfExists('users');
    }
}
