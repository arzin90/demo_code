<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsSpecialistSeenToProgramUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('program_users', function(Blueprint $table) {
            $table->boolean('is_specialist_seen')->default(false)->after('is_seen');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('program_users', function(Blueprint $table) {
            $table->dropColumn('is_specialist_seen');
        });
    }
}
