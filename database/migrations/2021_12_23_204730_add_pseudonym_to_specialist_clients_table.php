<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPseudonymToSpecialistClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('specialist_clients', function(Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'patronymic_name', 'gender', 'b_day']);
            $table->string('pseudonym')->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('specialist_clients', function(Blueprint $table) {
            $table->dropColumn(['pseudonym']);
        });
    }
}
