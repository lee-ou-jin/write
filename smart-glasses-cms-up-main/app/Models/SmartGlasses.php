<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;


class SmartGlasses extends Authenticatable
{
    use HasApiTokens, HasFactory, HasUuids;

    protected $table = 'smart_glasses';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function ($smartGlasses) {
            if (empty($smartGlasses->auth_code)) {
                do {
                    $authCode = strtoupper(Str::random(8));
                } while (self::isAuthCodeExists($authCode));

                $smartGlasses->auth_code = $authCode;
            }
        });

        static::created(function ($smartGlasses) {
        });
    }

    private static function isAuthCodeExists($authCode): bool
    {
        return Cache::rememberForever('auth_code_exists_' . $authCode, function () use ($authCode) {
            return self::where('auth_code', strtoupper($authCode))->exists();
        });
    }

    public function generateAuthCode($length = 8): void
    {
        do {
            $authCode = strtoupper(Str::random($length));
        } while (self::isAuthCodeExists($authCode));

        $this->auth_code = $authCode;
        $this->save();
    }

    public function verifyAuthCode($code): bool
    {
        return $this->auth_code === strtoupper($code);
    }

    public function ship(): BelongsTo
    {
        return $this->belongsTo(Ship::class);
    }

    public function shipsCompany(): BelongsTo
    {
        return $this->belongsTo(ShipsCompany::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
