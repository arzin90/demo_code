<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpecialistComment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'specialist_id', 'message', 'rate', 'is_deleted'];

    /**
     * @return BelongsTo
     */
    public function specialist()
    {
        return $this->belongsTo(Specialist::class)->with('user');
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
