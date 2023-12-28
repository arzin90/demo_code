<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWorkTypesToSpecialistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('specialists', function(Blueprint $table) {
            $table->boolean('online')->nullable()->default(false)->after('user_id');
            $table->boolean('offline')->nullable()->default(false)->after('online');
            $table->integer('location_id')->nullable()->unsigned()->after('offline');
            $table->foreign('location_id')->on('locations')->references('id')->nullOnDelete();
            $table->string('address')->nullable()->after('location_id');
            $table->string('link')->nullable()->after('address');
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
            $table->dropForeign(['location_id']);
            $table->dropColumn('online');
            $table->dropColumn('offline');
            $table->dropColumn('location_id');
            $table->dropColumn('address');
            $table->dropColumn('link');
        });
    }
}
