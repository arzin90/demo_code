<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialistFavorite extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'specialist_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function specialists()
    {
        return $this->belongsTo(Specialist::class)->with('user');
    }
}
