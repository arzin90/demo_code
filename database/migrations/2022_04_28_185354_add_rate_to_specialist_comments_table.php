<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddRateToSpecialistCommentsTable extends Migration
{
    private $_prefix = 'specialist_comment_';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('specialist_rates');

        Schema::table('specialist_comments', function(Blueprint $table) {
            $table->unsignedTinyInteger('rate')->after('message');
        });

        /* TRIGGER AFTER INSERT */
        DB::unprepared("
        DROP TRIGGER IF EXISTS `{$this->_prefix}create_rate`;
        CREATE TRIGGER {$this->_prefix}create_rate AFTER INSERT ON `specialist_comments` FOR EACH ROW
            BEGIN
                UPDATE specialists SET rate=(SELECT AVG(rate) FROM specialist_comments WHERE specialist_id = NEW.`specialist_id` and is_deleted=0), updated_at=NOW() WHERE specialists.id=NEW.`specialist_id`;
            END
        ");

        /* TRIGGER AFTER UPDATE */
        DB::unprepared("
        DROP TRIGGER IF EXISTS `{$this->_prefix}update_rate`;
        CREATE TRIGGER {$this->_prefix}update_rate AFTER UPDATE ON `specialist_comments` FOR EACH ROW
            BEGIN
                UPDATE specialists SET rate=(SELECT AVG(rate) FROM specialist_comments WHERE specialist_id = NEW.`specialist_id` and is_deleted=0), updated_at=NOW() WHERE specialists.id=NEW.`specialist_id`;
            END
        ");

        /* TRIGGER AFTER DELETE */
        DB::unprepared("
        DROP TRIGGER IF EXISTS `{$this->_prefix}delete_rate`;
        CREATE TRIGGER {$this->_prefix}delete_rate AFTER DELETE ON `specialist_comments` FOR EACH ROW
            BEGIN
                UPDATE specialists SET rate=(select AVG(rate) FROM specialist_comments WHERE specialist_id = OLD.`specialist_id`), updated_at=NOW() WHERE specialists.id=OLD.`specialist_id`;
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
        Schema::table('specialist_comments', function(Blueprint $table) {
            $table->dropColumn('rate');
        });

        DB::unprepared("DROP TRIGGER IF EXISTS `{$this->_prefix}create_rate`");
        DB::unprepared("DROP TRIGGER IF EXISTS `{$this->_prefix}update_rate`");
        DB::unprepared("DROP TRIGGER IF EXISTS `{$this->_prefix}delete_rate`");
    }
}
