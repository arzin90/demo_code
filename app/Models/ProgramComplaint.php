<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramComplaint extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'program_id', 'status', 'message'];

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
    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
