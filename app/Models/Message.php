<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Message extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $appends = ['file'];

    protected $fillable = ['from', 'to', 'message', 'replay', 'from_read', 'to_read', 'from_deleted', 'to_deleted', 'created_at'];

    /**
     * @param \DateTimeInterface $date
     *
     * @return string
     */
//    protected function serializeDate(DateTimeInterface $date)
//    {
//        return $date->setTimezone(new DateTimeZone('+0300'))->translatedFormat('Y M d h:i:s');
//    }

    /**
     * @return BelongsTo
     */
    public function from()
    {
        return $this->belongsTo(User::class, 'from')
            ->select(['id', 'status_id', 'first_name', 'last_name', 'patronymic_name']);
    }

    /**
     * @return BelongsTo
     */
    public function to()
    {
        return $this->belongsTo(User::class, 'to')
            ->select(['id', 'status_id', 'first_name', 'last_name', 'patronymic_name']);
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
