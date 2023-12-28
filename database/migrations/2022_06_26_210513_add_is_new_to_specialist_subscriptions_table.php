<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsNewToSpecialistSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('specialist_subscriptions', function(Blueprint $table) {
            $table->boolean('is_new')->default(1)->after('specialist_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('specialist_subscriptions', function(Blueprint $table) {
            $table->dropColumn('is_new');
        });
    }
}
