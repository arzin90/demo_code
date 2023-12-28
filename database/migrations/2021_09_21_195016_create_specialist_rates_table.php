<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSpecialistRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('specialist_rates', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('specialist_id')->nullable();
            $table->unsignedTinyInteger('rate');
            $table->timestamps();

            $table->unique(['user_id', 'specialist_id']);
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('specialist_id')->references('id')->on('specialists')->nullOnDelete();
        });

        /* TRIGGER AFTER INSERT */
        DB::unprepared('
        DROP TRIGGER IF EXISTS `create_rate`;
        CREATE TRIGGER create_rate AFTER INSERT ON `specialist_rates` FOR EACH ROW
            BEGIN
                UPDATE specialists SET rate=(SELECT AVG(rate) FROM specialist_rates WHERE specialist_id = NEW.`specialist_id`), updated_at=NOW() WHERE specialists.id=NEW.`specialist_id`;
            END
        ');

        /* TRIGGER AFTER UPDATE */
        DB::unprepared('
        DROP TRIGGER IF EXISTS `update_rate`;
        CREATE TRIGGER update_rate AFTER UPDATE ON `specialist_rates` FOR EACH ROW
            BEGIN
                UPDATE specialists SET rate=(SELECT AVG(rate) FROM specialist_rates WHERE specialist_id = NEW.`specialist_id`), updated_at=NOW() WHERE specialists.id=NEW.`specialist_id`;
            END
        ');

        /* TRIGGER AFTER DELETE */
        DB::unprepared('
        DROP TRIGGER IF EXISTS `delete_rate`;
        CREATE TRIGGER delete_rate AFTER DELETE ON `specialist_rates` FOR EACH ROW
            BEGIN
                UPDATE specialists SET rate=(select AVG(rate) FROM specialist_rates WHERE specialist_id = OLD.`specialist_id`), updated_at=NOW() WHERE specialists.id=OLD.`specialist_id`;
            END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('specialist_rates', function(Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['specialist_id']);
        });

        DB::unprepared('DROP TRIGGER IF EXISTS `create_rate`');
        DB::unprepared('DROP TRIGGER IF EXISTS `update_rate`');
        DB::unprepared('DROP TRIGGER IF EXISTS `delete_rate`');

        Schema::dropIfExists('specialist_rates');
    }
}
