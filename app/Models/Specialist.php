<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Specialist extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = ['status', 'user_id', 'phone', 'rate', 'online', 'offline', 'location_id', 'address', 'link', 'video', 'video_status'];

    protected $appends = ['is_favorite', 'is_my', 'comment_count', 'program_count', 'program_notification_count', 'subscribers_count', 'new_subscribers_count', 'is_subscribed'];

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
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * @return BelongsTo
     */
    public function favorite()
    {
        return $this->belongsTo(SpecialistFavorite::class, 'id', 'specialist_id');
    }

    /**
     * @return HasMany
     */
    public function educations()
    {
        return $this->hasMany(Education::class);
    }

    /**
     * @return HasMany
     */
    public function programs()
    {
        return $this->hasMany(Program::class);
    }

    /**
     * @return MorphMany
     */
    public function documents()
    {
        return $this->morphMany(config('media-library.media_model'), 'model')
            ->where('collection_name', '=', 'document')->
            orderByDesc('id');
    }

    /**
     * @return MorphMany
     */
    public function videos()
    {
        return $this->morphMany(config('media-library.media_model'), 'model')
            ->where('collection_name', '=', 'video')->
            orderByDesc('id');
    }

    /**
     * @return array
     */
    public function getDocumentAttribute()
    {
        $media = [];

        if (!empty($this->documents)) {
            foreach ($this->documents as $document) {
                $media[] = [
                    'id' => $document->id,
                    'url' => $document->getFullUrl(),
                    'size' => $document->human_readable_size,
                    'file_name' => $document->file_name,
                    'mime_type' => $document->mime_type,
                ];
            }
        }

        return $media;
    }

    /**
     * @return array
     */
    public function getVideoMediaAttribute()
    {
        $videos = [];

        if (!empty($this->videos)) {
            foreach ($this->videos as $video) {
                $videos[] = [
                    'id' => $video->id,
                    'url' => $video->getFullUrl(),
                    'size' => $video->human_readable_size,
                    'file_name' => $video->file_name,
                    'mime_type' => $video->mime_type,
                    'status' => $video->getCustomProperty('status'),
                ];
            }
        }

        return $videos;
    }

    /**
     * @return bool
     */
    public function getIsFavoriteAttribute()
    {
        return SpecialistFavorite::query()->where(['user_id' => auth()->id(), 'specialist_id' => $this->id])->exists();
    }

    /**
     * @return bool
     */
    public function getIsMyAttribute()
    {
        return SpecialistClient::query()->where(['user_id' => auth()->id(), 'specialist_id' => $this->id])->exists();
    }

    /**
     * @return int
     */
    public function getCommentCountAttribute()
    {
        return SpecialistComment::query()->where(['specialist_id' => $this->id, 'is_deleted' => 0])->count();
    }

    /**
     * @return BelongsToMany
     */
    public function specialties()
    {
        return $this->belongsToMany(Specialty::class, 'specialist_specialties', 'specialist_id', 'speciality_id');
    }

    /**
     * @return HasMany
     */
    public function clients()
    {
        return $this->hasMany(SpecialistClient::class)->with(['user']);
    }

    /**
     * @return HasMany
     */
    public function subscribers()
    {
        return $this->hasMany(SpecialistSubscription::class);
    }

    public function getIsSubscribedAttribute(): bool
    {
        return $this->subscribers()->where(['user_id' => auth()->id()])->exists();
    }

    /**
     * @return int
     */
    public function getSubscribersCountAttribute()
    {
        return $this->subscribers()->count();
    }

    /**
     * @return int
     */
    public function getNewSubscribersCountAttribute()
    {
        return $this->subscribers()->where(['is_new' => 1])->count();
    }

    /**
     * @return int
     */
    public function getProgramCountAttribute()
    {
        return $this->programs()->whereIn('status', [Status::ACTIVE, Status::FOR_CHECKING])->count();
    }

    /**
     * @return int
     */
    public function getProgramNotificationCountAttribute()
    {
        $program_ids = $this->programs()->pluck('id');

        return ProgramUser::query()->where(['is_payed' => 1, 'is_specialist_seen' => 0])->whereIn('program_id', $program_ids)->count();
    }

    public static function self(): Specialist
    {
        return new self();
    }
}
