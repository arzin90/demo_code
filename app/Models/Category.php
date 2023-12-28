<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    protected $hidden = ['id', 'created_at', 'updated_at', 'pivot'];

    /**
     * @return array<\Illuminate\Database\Eloquent\Builder>|\Illuminate\Database\Eloquent\Collection
     */
    public static function getList()
    {
        return self::query()->get(['id', 'name']);
    }
}
