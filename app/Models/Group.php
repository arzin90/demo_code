<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Group extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $appends = ['last_message', 'unread_count', 'message_count', 'member_count', 'is_muted', 'image_url'];

    protected $fillable = ['status', 'name', 'description', 'user_id'];

    protected $with = ['program', 'image'];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function members()
    {
        return $this->hasMany(GroupMember::class)->where(['is_deleted' => 0, 'status' => Status::ACTIVE]);
    }

    /**
     * @return HasOne
     */
    public function program()
    {
        return $this->hasOne(Program::class);
    }

    /**
     * @return MorphOne
     */
    public function image()
    {
        return $this->morphOne(config('media-library.media_model'), 'model')
            ->where('collection_name', '=', 'image')
            ->orderByDesc('id');
    }

    /**
     * @throws InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->quality(90);
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->relationLoaded('image') && $this->image) {
            return $this->image->getFullUrl('thumb');
        } else {
            return null;
        }
    }

    /**
     * @return Builder|Model|object|null
     */
    public function getLastMessageAttribute()
    {
        return GroupMessage::query()->with('user')->where(['group_id' => $this->id])->whereNotExists(function($query) {
            return $query->where(['is_deleted' => 1, 'user_id' => auth()->id()]);
        })->orderByDesc('created_at')->limit(1)->first();
    }

    /**
     * @return int
     */
    public function getUnreadCountAttribute()
    {
        $auth_id = auth()->id();

        return GroupMessageEvent::query()
            ->leftJoin('group_messages', 'group_message_id', '=', 'group_messages.id')
            ->where(['group_id' => $this->id, 'is_read' => 0, 'group_message_events.is_deleted' => 0])
            ->where('group_messages.user_id', '<>', $auth_id)
            ->where(['group_message_events.user_id' => $auth_id])
            ->count();
    }

    /**
     * @return int
     */
    public function getMessageCountAttribute()
    {
        $auth_id = auth()->id();

        $sent_message_count = GroupMessage::query()
            ->where(['user_id' => $auth_id, 'group_id' => $this->id, 'is_deleted' => 0])
            ->count();

        $received_message_count = GroupMessageEvent::query()
            ->leftJoin('group_messages', 'group_message_id', '=', 'group_messages.id')
            ->where(['group_id' => $this->id, 'group_message_events.is_deleted' => 0])
            ->where('group_messages.user_id', '<>', $auth_id)
            ->where(['group_message_events.user_id' => $auth_id])
            ->count();

        return $sent_message_count + $received_message_count;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function groupMessages()
    {
        return $this->hasMany(GroupMessage::class);
    }

    /**
     * @return bool
     */
    public function getIsMutedAttribute()
    {
        return MutedGroup::query()->where(['user_id' => auth()->id(), 'group_id' => $this->id])->exists();
    }

    /**
     * @return int
     */
    public function getMemberCountAttribute()
    {
        return $this->members()->count();
    }
}
