<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRequestedByUserToSpecialtiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('specialties', function(Blueprint $table) {
            $table->unsignedBigInteger('requested_by_user')->nullable()->after('requested_by');

            $table->foreign('requested_by_user')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('specialties', function(Blueprint $table) {
            $table->dropForeign(['requested_by_user']);
            $table->dropColumn('requested_by_user');
        });
    }
}
