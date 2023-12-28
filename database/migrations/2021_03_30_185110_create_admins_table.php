<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function(Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('patronymic_name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('phone')->unique()->nullable();
            $table->string('avatar')->nullable();
            $table->string('password')->nullable();
            $table->timestamps();
        });

        DB::table('admins')->insert([
            'first_name' => 'Coach',
            'last_name' => 'Coach',
            'patronymic_name' => 'Coach',
            'email' => 'coachcoach@mail.ru',
            'phone' => '+7123456789',
            'password' => Hash::make('$#!#Loach123$'),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
