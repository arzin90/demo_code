<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = ['city', 'popular'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function news()
    {
        return $this->hasMany(News::class);
    }

    /**
     * @return array<\Illuminate\Database\Eloquent\Builder>|\Illuminate\Database\Eloquent\Collection
     */
    public static function getList()
    {
        return self::query()->get(['id', 'city']);
    }
}
