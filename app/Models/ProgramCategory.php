<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'specialist_id', 'status', 'name',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function specialist()
    {
        return $this->belongsTo(Specialist::class);
    }

    /**
     * @return array<string>
     */
    public static function getStatus(): array
    {
        return [
            Status::PENDING => 'B ожидании',
            Status::ACTIVE => 'Активный',
        ];
    }
}
