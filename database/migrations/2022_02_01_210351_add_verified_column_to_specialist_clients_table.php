<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVerifiedColumnToSpecialistClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('specialist_clients', function(Blueprint $table) {
            $table->boolean('notified')->nullable()->after('verified');
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
            $table->dropColumn(['notified']);
        });
    }
}
