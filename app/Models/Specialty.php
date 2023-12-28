<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialty extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status', 'requested_by'];
    protected $hidden = ['requested_by', 'requested_by_user', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function specialist()
    {
        return $this->belongsTo(Specialist::class, 'requested_by')->with('user');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'requested_by_user');
    }
}
