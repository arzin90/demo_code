<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Education extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $appends = ['diploma_media'];

    protected $fillable = ['status', 'specialist_id', 'institution', 'faculty', 'specialty', 'graduation_at', 'deleted_at'];

    protected $hidden = [
        'diploma',
    ];

    protected $casts = [
        'graduation_at' => 'date:d-m-Y',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function specialist()
    {
        return $this->belongsTo(Specialist::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function diploma()
    {
        return $this->morphMany(config('media-library.media_model'), 'model')->where('collection_name', '=', 'diploma')->orderByDesc('id');
    }

    /**
     * @return array
     */
    public function getDiplomaMediaAttribute()
    {
        $media = [];

        if (!empty($this->diploma)) {
            foreach ($this->diploma as $diploma) {
                $media[] = [
                    'id' => $diploma->id,
                    'url' => $diploma->getFullUrl(),
                    'size' => $diploma->human_readable_size,
                    'file_name' => $diploma->file_name,
                    'mime_type' => $diploma->mime_type,
                ];
            }
        }

        return $media;
    }
}
