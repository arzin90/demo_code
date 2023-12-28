<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramFavorite extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'program_id'];

    /**
     * @return BelongsTo
     */
    public function programs()
    {
        return $this->belongsTo(Program::class);
    }
}
