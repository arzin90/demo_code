<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSpecialist extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'specialist_id', 'user_id', 'status', 'pseudonym', 'notified',
    ];

    protected $with = ['specialist.user', 'specialist.specialties'];

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
