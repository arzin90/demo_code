<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'status', 'key', 'content'];

    public function getStatusAttribute($value): string
    {
        return strtolower($value);
    }

    public function getKeyAttribute($value): string
    {
        return strtolower($value);
    }
}
