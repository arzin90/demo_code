<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class News extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $appends = ['image_url'];

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';

    protected $fillable = ['status_id', 'title', 'short_description', 'description', 'view_count', 'location_id'];

    public $with = ['image', 'categories', 'location'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['image', 'location_id'];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'news_categories');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function newsCategory()
    {
        return $this->hasOne(NewsCategory::class, 'news_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * @return array<string>
     */
    public static function getStatus(): array
    {
        return [
            self::STATUS_PENDING => 'B ожидании',
            self::STATUS_ACTIVE => 'Активный',
        ];
    }

    public function image(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(config('media-library.media_model'), 'model')->where('collection_name', '=', 'image')->orderByDesc('id');
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->relationLoaded('image') && $this->image) {
            return $this->image->getFullUrl();
        } else {
            return asset('assets/images/lifecoach.png');
        }
    }

    public static function self(): News
    {
        return new self();
    }
}
