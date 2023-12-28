<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialistSpecialty extends Model
{
    use HasFactory;

    protected $fillable = ['specialist_id', 'speciality_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function specialists()
    {
        return $this->belongsTo(Specialist::class, 'specialist_id')->with('user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function specialties()
    {
        return $this->belongsTo(Specialty::class, 'speciality_id');
    }
}
