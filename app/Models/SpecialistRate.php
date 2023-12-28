<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialistRate extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'specialist_id', 'rate'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function specialist()
    {
        return $this->belongsTo(Specialist::class, 'specialist_id')->with('user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
