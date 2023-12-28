<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpecialistSubscription extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'specialist_id', 'is_new'];

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
    public function specialist()
    {
        return $this->belongsTo(Specialist::class)->whereIn('status', [Status::ACTIVE, Status::FOR_CHECKING])
            ->with('user', function($query) {
                $query->where('status_id', UserStatus::ACTIVE);
            });
    }
}
