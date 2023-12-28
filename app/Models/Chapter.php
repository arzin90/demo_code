<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;

    public const ONLINE = 'online';

    public const OFFLINE = 'offline';

    protected $fillable = [
        'specialist_id', 'status', 'type', 'name',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function specialist()
    {
        return $this->belongsTo(Specialist::class);
    }

    /**
     * @return array<string>
     */
    public static function getStatus(): array
    {
        return [
            Status::PENDING => 'B ожидании',
            Status::ACTIVE => 'Активный',
        ];
    }

    /**
     * @return array<string>
     */
    public static function getType(): array
    {
        return [
            self::ONLINE => 'Онлайн',
            self::OFFLINE => 'Офлайн',
        ];
    }
}
