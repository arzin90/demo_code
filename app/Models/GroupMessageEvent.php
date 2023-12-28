<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupMessageEvent extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'group_message_id', 'is_read', 'is_deleted'];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function groupMessage()
    {
        return $this->belongsTo(GroupMessage::class);
    }
}
