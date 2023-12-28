<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateAfterDeleteGroupMembersTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            DROP TRIGGER IF EXISTS `after_delete_group_members`;
            CREATE TRIGGER `after_delete_group_members` AFTER UPDATE ON `group_members`
            FOR EACH ROW
                BEGIN
                    IF NEW.status = 'deleted' OR NEW.is_deleted = 1 THEN
                        UPDATE `group_messages` SET is_deleted = 1 WHERE group_id = OLD.group_id AND user_id = OLD.user_id;
                        UPDATE `group_message_events` SET is_deleted = 1 WHERE group_message_id in (SELECT id from `group_messages` WHERE group_id = OLD.group_id) AND user_id = OLD.user_id;
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
        DB::unprepared('DROP TRIGGER IF EXISTS `after_delete_group_members`;');
    }
}
