<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpecialistClient extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'specialist_id', 'user_id', 'status', 'pseudonym', 'email', 'phone', 'verified', 'notified', 'about',
    ];

    protected $with = ['user'];

    /**
     * @return BelongsTo
     */
    public function specialist()
    {
        return $this->belongsTo(Specialist::class);
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
