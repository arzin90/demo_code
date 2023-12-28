<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Program extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $appends = ['gallery_media', 'comment_count', 'rate_count', 'notification_count', 'is_favorite', 'is_my', 'my_rate'];

    protected $fillable = [
        'specialist_id', 'presenter', 'presenter_id', 'group_id', 'status', 'is_online', 'name', 'location_id', 'media_id', 'price', 'sale_price',
        'link', 'member_count', 'description', 'time_zone',
    ];

    protected $hidden = [
        'gallery',
    ];

    protected $with = ['presenter_user.specialist.specialties'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function gallery()
    {
        return $this->morphMany(config('media-library.media_model'), 'model')->where('collection_name', '=', 'gallery')->orderByDesc('id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function specialist()
    {
        return $this->belongsTo(Specialist::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function presenter_user()
    {
        return $this->belongsTo(User::class, 'presenter_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * @return BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(ProgramCategory::class, 'program_program_categories');
    }

    /**
     * @return HasMany
     */
    public function programDates()
    {
        return $this->hasMany(ProgramDate::class);
    }

    /**
     * @return BelongsToMany
     */
    public function programChapters()
    {
        return $this->belongsToMany(Chapter::class, 'program_chapters');
    }

    /**
     * @return HasMany
     */
    public function programComments()
    {
        return $this->hasMany(ProgramComment::class);
    }

    /**
     * @return HasMany
     */
    public function programComplaints()
    {
        return $this->hasMany(ProgramComplaint::class);
    }

    /**
     * @return BelongsTo
     */
    public function favorite()
    {
        return $this->belongsTo(ProgramFavorite::class, 'id', 'program_id');
    }

    /**
     * @return HasMany
     */
    public function programRates()
    {
        return $this->hasMany(ProgramRate::class);
    }

    /**
     * The roles that belong to the user.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'program_users')->withPivot(['is_payed', 'is_seen'])->withTimestamps();
    }

    /**
     * @return Collection
     */
    public function getMyRateAttribute()
    {
        $rate = $this->programRates()->where(['user_id' => auth()->id()])->pluck('rate');

        if ($rate->count()) {
            return $rate[0];
        }

        return null;
    }

    /**
     * @return bool
     */
    public function getIsFavoriteAttribute()
    {
        return ProgramFavorite::query()->where(['user_id' => auth()->id(), 'program_id' => $this->id])->exists();
    }

    /**
     * @return bool
     */
    public function getIsMyAttribute()
    {
        return self::query()->where(['id' => $this->id])->whereHas('specialist', function($query) {
            $query->where(['user_id' => auth()->id()]);
        })->exists();
    }

    /**
     * @return array
     */
    public function getGalleryMediaAttribute()
    {
        $media = [];

        if (!empty($this->gallery)) {
            foreach ($this->gallery as $image) {
                $media[] = [
                    'id' => $image->id,
                    'url' => $image->getFullUrl(),
                    'size' => $image->human_readable_size,
                    'file_name' => $image->file_name,
                    'mime_type' => $image->mime_type,
                ];
            }
        }

        return $media;
    }

    /**
     * @return int
     */
    public function getCommentCountAttribute()
    {
        return $this->programComments()->where(['is_deleted' => 0])->count();
    }

    /**
     * @return int
     */
    public function getRateCountAttribute()
    {
        return $this->programRates()->count();
    }

    /**
     * @return int
     */
    public function getNotificationCountAttribute()
    {
        return $this->users()->wherePivot('is_payed', '=', 1)->wherePivot('is_specialist_seen', '=', 0)->count();
    }
}
