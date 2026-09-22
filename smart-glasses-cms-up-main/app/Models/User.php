<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasUuids;
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use HasRoles;
    use TwoFactorAuthenticatable;
    use AuthenticationLoggable;
    use SoftDeletes;

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'roleable_type',
        'roleable_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public static function boot()
    {
        parent::boot();

        //        self::creating(function ($user) {
        //            if (empty($user->agora_uid)) {
        //                $user->agora_uid = self::generateUniqueAgoraUid();
        //            }
        //        });

        self::created(function ($user) {
            $rolable_type = $user->roleable_type;
            if (!$rolable_type) {
                $user->role = '관리자';
                $user->save();
                $user->syncRoles(['super-admin']);
                return;
            }

            if ($rolable_type === ShipsCompany::class) {
                $user->role = '선사';
            } else {
                $user->role = '업체';
            }
            $user->save();
        });

        self::deleted(function ($user) {
            $user->email = sprintf("DELETED::%s::%s", Str::random(8), $user->email);
            $user->save();
        });

        self::restored(function ($user) {
            $user->email = substr($user->email, 19);
            $existsCount = User::query()->where('email', 'like', $user->email . '%')->count();
            if ($existsCount > 0) {
                $user->email .= "-" . $existsCount;
            }
            $user->save();
        });
    }

    public function userAccounts(): HasMany
    {
        return $this->hasMany(UserAccount::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    public function roleable(): MorphTo
    {
        return $this->morphTo();
    }

    //    public function meetings(): HasMany
    //    {
    //        return $this->hasMany(Meeting::class);
    //    }

    public function meetings(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Meeting::class,
            table: 'meeting_users',
            foreignPivotKey: 'user_id',
            relatedPivotKey: 'meeting_id'
        )
            ->withTimestamps();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public static function generateUniqueAgoraUid(): string
    {
        do {
            $value = random_int(0, 4294967295);
        } while (self::query()->where('agora_uid', $value)->exists());

        return $value;
    }
}
