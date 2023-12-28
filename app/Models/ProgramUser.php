<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramUser extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'program_id', 'is_payed', 'is_seen'];
}
