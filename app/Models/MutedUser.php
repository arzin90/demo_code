<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MutedUser extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'muted_user_id'];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * @return BelongsTo
     */
    public function muted()
    {
        return $this->belongsTo('App\Models\User');
    }
}
