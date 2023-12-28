<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVideoStatusToSpecialistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('specialists', function(Blueprint $table) {
            $table->enum('video_status', ['pending', 'active', 'blocked', 'deleted', 'for_checking'])->nullable()->after('video');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('specialists', function(Blueprint $table) {
            $table->dropColumn('video_status');
        });
    }
}
