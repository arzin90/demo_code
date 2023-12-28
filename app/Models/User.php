<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements MustVerifyEmail, HasMedia, JWTSubject
{
    use HasFactory;
    use Notifiable;
    use InteractsWithMedia;

    protected $appends = ['avatar_url', 'last_message', 'unread_count', 'unread_count_all', 'message_count',
        'program_count', 'subscription_count', 'unseen_program_count', 'is_user_muted', 'is_client', 'is_info_added', 'is_my_specialist'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'status_id', 'patronymic_name', 'email', 'phone', 'location_id',
        'address', 'password', 'gender', 'b_day', 'content', 'url', 'is_muted', 'verified_at', 'last_visit',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public $with = ['avatar', 'status'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'verified_at' => 'datetime',
        'b_day' => 'date:d-m-Y',
    ];

    /**
     * @return BelongsTo
     */
    public function status()
    {
        return $this->belongsTo('App\Models\UserStatus', 'status_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function location()
    {
        return $this->belongsTo('App\Models\Location', 'location_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function specialist()
    {
        return $this->hasOne(Specialist::class);
    }

    /**
     * @return HasOne
     */
    public function specialistIsActive()
    {
        return $this->hasOne(Specialist::class)->whereIn('status', [Status::FOR_CHECKING, Status::ACTIVE]);
    }

    /**
     * @return Builder|HasMany
     */
    public function messagesFrom()
    {
        return $this->hasMany(Message::class, 'from');
    }

    /**
     * @return Builder|HasMany
     */
    public function messagesTo()
    {
        return $this->hasMany(Message::class, 'to');
    }

    /**
     * @return HasMany
     */
    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    /**
     * @return HasMany
     */
    public function groupMembares()
    {
        return $this->hasMany(GroupMember::class);
    }

    /**
     * @return BelongsToMany
     */
    public function groupMembersMany()
    {
        return $this->belongsToMany(Group::class, 'group_members');
    }

    /**
     * @return HasMany
     */
    public function groupMessages()
    {
        return $this->hasMany(GroupMessage::class);
    }

    /**
     * @return BelongsToMany
     */
    public function groupMessagesMany()
    {
        return $this->belongsToMany(Group::class, 'group_messages')->withPivot(['message', 'is_deleted'])->withTimestamps();
    }

    /**
     * @return HasMany
     */
    public function groupMessageEvents()
    {
        return $this->hasMany(GroupMessageEvent::class);
    }

    /**
     * @return BelongsToMany
     */
    public function groupMessageEventsMany()
    {
        return $this->belongsToMany(GroupMessage::class, 'group_message_events')->withPivot(['is_read', 'is_deleted'])->withTimestamps();
    }

    /**
     * @return HasMany
     */
    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }

    /**
     * @return HasMany
     */
    public function favorites()
    {
        return $this->hasMany(SpecialistFavorite::class);
    }

    /**
     * @return HasMany
     */
    public function programFavorites()
    {
        return $this->hasMany(ProgramFavorite::class);
    }

    /**
     * @return HasMany
     */
    public function specialistSubscription()
    {
        return $this->hasMany(SpecialistSubscription::class);
    }

    /**
     * @return HasMany
     */
//    public function specialistRates()
//    {
//        return $this->hasMany(SpecialistRate::class);
//    }

    /**
     * @return HasMany
     */
    public function specialistComments()
    {
        return $this->hasMany(SpecialistComment::class);
    }

    /**
     * @return Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getAuthUser()
    {
        $auth = auth();

        if ($auth->user()->specialist()->exists()) {
            return $this->query()->where(['id' => $auth->id()])
                ->with(['status', 'specialist.specialties', 'programs.specialist.user', 'location'])
                ->first();
        }

        return $this->query()->where(['id' => $auth->id()])
            ->with(['status', 'specialist', 'programs.specialist.user', 'location'])
            ->first();
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * @return string
     */
    public function getFullNameAttribute()
    {
        return sprintf('%s %s %s', $this->first_name, $this->last_name, $this->patronymic_name ?: '');
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public function getFirstNameAttribute($value)
    {
        return Str::ucfirst($value);
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public function getLastNameAttribute($value)
    {
        return Str::ucfirst($value);
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public function getPatronymicNameAttribute($value)
    {
        if ($value) {
            return Str::ucfirst($value);
        }

        return '';
    }

    /**
     * @return string
     */
    public function getIsInfoAddedAttribute()
    {
        return (bool) $this->first_name && (bool) $this->last_name && (bool) $this->phone && (bool) $this->gender && (bool) $this->b_day;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function avatar()
    {
        return $this->morphOne(config('media-library.media_model'), 'model')->where('collection_name', '=', 'avatar')->orderByDesc('id');
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->relationLoaded('avatar') && $this->avatar) {
            return $this->avatar->getFullUrl();
        } else {
            return null;
        }
    }

    /**
     * @param mixed $user_id
     *
     * @return Builder
     */
    public function getUserMessages($user_id)
    {
        return Message::query()->where(['from' => $this->id, 'to' => $user_id, 'to_deleted' => 0])
            ->orWhere(function($query) use ($user_id) {
                $query->where(['to' => $this->id, 'from' => $user_id, 'from_deleted' => 0]);
            });
    }

    /**
     * @return Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getLastMessageAttribute()
    {
        $owner_id = auth()->id();

        if (request()->route('specialist')) {
            $owner_id = Specialist::find(request()->route('specialist'))->user_id;
        }

        if (request()->route('user')) {
            $owner_id = request()->route('user');
        }

        return $this->getUserMessages($owner_id)->orderByDesc('created_at')->limit(1)->first();
    }

    /**
     * @return int
     */
    public function getUnreadCountAttribute()
    {
        return Message::query()->where(['from' => $this->id, 'to' => auth()->id(), 'to_read' => 0, 'to_deleted' => 0])->count();
    }

    /**
     * @return int
     */
    public function getUnreadCountAllAttribute()
    {
        $unread_message_count = Message::query()->where(['to' => $this->id, 'to_read' => 0, 'to_deleted' => 0])->count();
        $unread_group_message_count = GroupMessageEvent::query()->where(['user_id' => $this->id, 'is_read' => 0, 'is_deleted' => 0])->count();

        return $unread_message_count + $unread_group_message_count;
    }

    /**
     * @return int
     */
    public function getMessageCountAttribute()
    {
        $user_id = $this->id;
        $auth_user_id = auth()->id();
        $sender_message_count = GroupMessage::query()->where(['user_id' => $auth_user_id, 'is_deleted' => 0])->count();
        $group_message_count = $sender_message_count + GroupMessageEvent::query()->where(['user_id' => $auth_user_id, 'is_deleted' => 0])->count();

        return $group_message_count + Message::query()->where(['from' => $user_id, 'to' => $auth_user_id, 'to_deleted' => 0])
            ->orWhere(function($query) use ($user_id, $auth_user_id) {
                return $query->where(['from' => $auth_user_id, 'to' => $user_id, 'from_deleted' => 0]);
            })->count();
    }

    /**
     * @return int
     */
    public function getProgramCountAttribute()
    {
        return $this->programs()->wherePivot('is_payed', 1)->count();
    }

    /**
     * @return int
     */
    public function getUnseenProgramCountAttribute()
    {
        return $this->programs()->wherePivot('is_payed', 1)->wherePivot('is_seen', 0)->count();
    }

    /**
     * @return bool
     */
    public function getIsUserMutedAttribute()
    {
        return MutedUser::query()->where(['user_id' => auth()->id(), 'muted_user_id' => $this->id])->exists();
    }

    /**
     * @return bool
     */
    public function getIsClientAttribute()
    {
        $specialist_id = null;

        if (auth('api')->user()) {
            $specialist = auth('api')->user()->specialistIsActive()->first();
            $specialist_id = !empty($specialist) ? $specialist->id : null;
        }

        if ($specialist_id) {
            return SpecialistClient::query()->where(['user_id' => $this->id, 'specialist_id' => $specialist_id])->exists();
        }

        return false;
    }

    /**
     * @return bool
     */
    public function getIsMySpecialistAttribute()
    {
        $specialist = $this->specialistIsActive()->first();
        $specialist_id = !empty($specialist) ? $specialist->id : null;

        if ($specialist_id) {
            return UserSpecialist::query()->where(['user_id' => auth()->id(), 'specialist_id' => $specialist_id])->exists();
        }

        return false;
    }

    /**
     * @return int
     */
    public function getSubscriptionCountAttribute()
    {
        return $this->specialistSubscription()->count();
    }

    /**
     * @return BelongsToMany
     */
    public function programs()
    {
        return $this->belongsToMany(Program::class, 'program_users')->withPivot(['is_payed', 'is_seen'])->withTimestamps();
    }

    /**
     * @return HasMany
     */
    public function programRates()
    {
        return $this->hasMany(ProgramRate::class);
    }

    /**
     * @return HasMany
     */
    public function programComments()
    {
        return $this->hasMany(ProgramComment::class);
    }

    /**
     * @return HasMany
     */
    public function mySpecialists()
    {
        return $this->hasMany(UserSpecialist::class);
    }

    /**
     * @return HasOne
     */
    public function programUser()
    {
        return $this->hasOne(ProgramUser::class);
    }

    /**
     * @return User
     */
    public static function self()
    {
        return new self();
    }
}
