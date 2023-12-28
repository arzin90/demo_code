<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStatus extends Model
{
    use HasFactory;

    public const PENDING = 1;

    public const ACTIVE = 2;

    public const BLOCKED = 3;

    public const DELETED = 4;

    protected $table = 'user_status';

    protected $fillable = [
        'name',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany('App\Models\User', 'status_id');
    }

    /**
     * @param bool $only_key
     *
     * @return array
     */
    public static function getList($only_key = false)
    {
        if ($only_key) {
            return [self::PENDING, self::ACTIVE, self::BLOCKED];
        }

        return [
            self::PENDING => __('messages.status_pending_user'),
            self::ACTIVE => __('messages.status_active'),
            self::BLOCKED => __('messages.status_blocked'),
        ];
    }
}
