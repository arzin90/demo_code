<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

class VerifyCode extends Model
{
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'email', 'phone', 'type', 'token', 'code', 'is_verified',
    ];

    /**
     * @return bool
     */
    public function isValidToken($token)
    {
        return $this->query()
            ->where('updated_at', '>=', Carbon::now()->addHours(-1)->toDateTimeString())
            ->where(['token' => $token])
            ->exists();
    }

    /**
     * @return bool
     */
    public function checkCode($token, $code)
    {
        return $this->query()
            ->where(['token' => $token, 'code' => $code])
            ->exists();
    }

    /**
     * @return VerifyCode
     */
    public static function self()
    {
        return new self();
    }
}
