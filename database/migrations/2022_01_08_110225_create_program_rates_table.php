<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateProgramRatesTable extends Migration
{
    private $_prefix = 'program_';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('program_rates', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->unsignedTinyInteger('rate');
            $table->timestamps();

            $table->unique(['user_id', 'program_id']);
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('program_id')->references('id')->on('programs')->nullOnDelete();
        });

        /* TRIGGER AFTER INSERT */
        DB::unprepared("
        DROP TRIGGER IF EXISTS `{$this->_prefix}create_rate`;
        CREATE TRIGGER {$this->_prefix}create_rate AFTER INSERT ON `program_rates` FOR EACH ROW
            BEGIN
                UPDATE programs SET rate=(SELECT AVG(rate) FROM program_rates WHERE program_id = NEW.`program_id`), updated_at=NOW() WHERE programs.id=NEW.`program_id`;
            END
        ");

        /* TRIGGER AFTER UPDATE */
        DB::unprepared("
        DROP TRIGGER IF EXISTS `{$this->_prefix}update_rate`;
        CREATE TRIGGER {$this->_prefix}update_rate AFTER UPDATE ON `program_rates` FOR EACH ROW
            BEGIN
                UPDATE programs SET rate=(SELECT AVG(rate) FROM program_rates WHERE program_id = NEW.`program_id`), updated_at=NOW() WHERE programs.id=NEW.`program_id`;
            END
        ");

        /* TRIGGER AFTER DELETE */
        DB::unprepared("
        DROP TRIGGER IF EXISTS `{$this->_prefix}delete_rate`;
        CREATE TRIGGER {$this->_prefix}delete_rate AFTER DELETE ON `program_rates` FOR EACH ROW
            BEGIN
                UPDATE programs SET rate=(select AVG(rate) FROM program_rates WHERE program_id = OLD.`program_id`), updated_at=NOW() WHERE programs.id=OLD.`program_id`;
            END
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('program_rates', function(Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['program_id']);
        });

        DB::unprepared("DROP TRIGGER IF EXISTS `{$this->_prefix}create_rate`");
        DB::unprepared("DROP TRIGGER IF EXISTS `{$this->_prefix}update_rate`");
        DB::unprepared("DROP TRIGGER IF EXISTS `{$this->_prefix}delete_rate`");

        Schema::dropIfExists('program_rates');
    }
}
