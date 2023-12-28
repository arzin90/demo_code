<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramProgramCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id', 'program_category_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(ProgramCategory::class, 'program_category_id');
    }
}
