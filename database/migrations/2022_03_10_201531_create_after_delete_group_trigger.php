<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateAfterDeleteGroupTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            DROP TRIGGER IF EXISTS `after_delete_group`;
            CREATE TRIGGER `after_delete_group` AFTER UPDATE ON `groups` FOR EACH ROW
                BEGIN
                    IF NEW.status = 'deleted' THEN
                        UPDATE `group_members` SET is_deleted = 1 WHERE group_id = OLD.id;
                        UPDATE `group_messages` SET is_deleted = 1 WHERE group_id = OLD.id;
                        UPDATE `group_message_events` SET is_deleted = 1 WHERE group_message_id in (SELECT id from `group_messages` WHERE group_id = OLD.id);
                    END IF;
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
        DB::unprepared('DROP TRIGGER IF EXISTS `after_delete_group`;');
    }
}
