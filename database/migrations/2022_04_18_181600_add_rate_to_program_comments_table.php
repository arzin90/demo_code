<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddRateToProgramCommentsTable extends Migration
{
    private $_prefix = 'program_';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS `{$this->_prefix}create_rate`");
        DB::unprepared("DROP TRIGGER IF EXISTS `{$this->_prefix}update_rate`");
        DB::unprepared("DROP TRIGGER IF EXISTS `{$this->_prefix}delete_rate`");

        Schema::table('program_comments', function(Blueprint $table) {
            $table->unsignedTinyInteger('rate')->after('message');
        });

        /* TRIGGER AFTER INSERT */
        DB::unprepared("
        DROP TRIGGER IF EXISTS `{$this->_prefix}comment_create_rate`;
        CREATE TRIGGER {$this->_prefix}comment_create_rate AFTER INSERT ON `program_comments` FOR EACH ROW
            BEGIN
                UPDATE programs SET rate=(SELECT AVG(rate) FROM program_comments WHERE program_id = NEW.`program_id` and is_deleted = 0), updated_at=NOW() WHERE programs.id=NEW.`program_id`;
            END
        ");

        /* TRIGGER AFTER UPDATE */
        DB::unprepared("
        DROP TRIGGER IF EXISTS `{$this->_prefix}comment_update_rate`;
        CREATE TRIGGER {$this->_prefix}comment_update_rate AFTER UPDATE ON `program_comments` FOR EACH ROW
            BEGIN
                UPDATE programs SET rate=(SELECT AVG(rate) FROM program_comments WHERE program_id = NEW.`program_id` and is_deleted = 0), updated_at=NOW() WHERE programs.id=NEW.`program_id`;
            END
        ");

        /* TRIGGER AFTER DELETE */
        DB::unprepared("
        DROP TRIGGER IF EXISTS `{$this->_prefix}comment_delete_rate`;
        CREATE TRIGGER {$this->_prefix}comment_delete_rate AFTER DELETE ON `program_comments` FOR EACH ROW
            BEGIN
                UPDATE programs SET rate=(select AVG(rate) FROM program_comments WHERE program_id = OLD.`program_id` and is_deleted = 0), updated_at=NOW() WHERE programs.id=OLD.`program_id`;
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
        Schema::table('program_comments', function(Blueprint $table) {
            $table->dropColumn('rate');
        });

        DB::unprepared("DROP TRIGGER IF EXISTS `{$this->_prefix}comment_create_rate`");
        DB::unprepared("DROP TRIGGER IF EXISTS `{$this->_prefix}comment_update_rate`");
        DB::unprepared("DROP TRIGGER IF EXISTS `{$this->_prefix}comment_delete_rate`");
    }
}
