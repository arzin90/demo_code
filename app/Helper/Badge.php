<?php

namespace App\Helper;

use App\Constants\Status;

class Badge
{
    /**
     * @return string
     */
    public static function getByStatusId($id)
    {
        $badge = '';

        switch ($id) {
            case 1:
                $badge = sprintf("<span class='badge badge-warning'>%s</span>", __('messages.status_pending_user'));
                break;
            case 2:
                $badge = sprintf("<span class='badge badge-success'>%s</span>", __('messages.status_active'));
                break;
            case 3:
                $badge = sprintf("<span class='badge badge-danger'>%s</span>", __('messages.status_blocked'));
                break;
            case 4:
                $badge = sprintf("<span class='badge badge-danger'>%s</span>", __('messages.status_deleted'));
        }

        return $badge;
    }

    /**
     * @return string
     */
    public static function getByStatusName($name)
    {
        $badge = '';

        switch ($name) {
            case Status::PENDING:
                $badge = sprintf("<span class='badge badge-warning'>%s</span>", __('messages.status_pending'));
                break;
            case Status::ACTIVE:
                $badge = sprintf("<span class='badge badge-success'>%s</span>", __('messages.status_active'));
                break;
            case Status::INACTIVE:
                $badge = sprintf("<span class='badge badge-info'>%s</span>", __('messages.status_inactive'));
                break;
            case Status::BLOCKED:
                $badge = sprintf("<span class='badge badge-danger'>%s</span>", __('messages.status_blocked'));
                break;
            case Status::DELETED:
                $badge = sprintf("<span class='badge badge-danger'>%s</span>", __('messages.status_deleted'));
                break;
            case Status::FOR_CHECKING:
                $badge = sprintf("<span class='badge badge-info'>%s</span>", __('messages.status_for_checking'));
        }

        return $badge;
    }
}
