<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVerifyCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verify_codes', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->enum('type', ['email', 'phone'])->default('email');
            $table->string('token')->unique();
            $table->string('code', 10);
            $table->boolean('is_verified')->default(false);
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
        Schema::dropIfExists('verify_codes');
    }
}
