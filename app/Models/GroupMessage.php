<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class GroupMessage extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $appends = ['file'];

    protected $fillable = ['user_id', 'group_id', 'message', 'is_deleted'];

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
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * @return HasMany
     */
    public function groupMessageEvents()
    {
        return $this->hasMany(GroupMessageEvent::class);
    }

    /**
     * Get the not deleted messages
     *
     * @return HasMany
     */
    public function scopeActiveMessages()
    {
        return $this->groupMessageEvents()->where(['is_deleted' => 0, 'user_id' => auth()->id()]);
    }

    /**
     * @return MorphOne
     */
    public function fileMedia()
    {
        return $this->morphOne(config('media-library.media_model'), 'model')->where('collection_name', '=', 'file')->orderByDesc('id');
    }

    /**
     * @return null
     */
    public function getFileAttribute()
    {
        if ($this->fileMedia) {
            return [
                'url' => $this->fileMedia->getFullUrl(),
                'size' => $this->fileMedia->human_readable_size,
            ];
        } else {
            return null;
        }
    }
}
