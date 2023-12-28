<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToEducationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('education', function(Blueprint $table) {
            $table->enum('status', ['pending', 'active', 'blocked', 'deleted', 'for_checking'])->default('pending')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('education', function(Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
