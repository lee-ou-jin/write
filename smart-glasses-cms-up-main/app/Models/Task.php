<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use AmuzPackages\VimeoField\Models\VimeoVideo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    protected $with = ['equipment'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            if (empty($task->code)) {
                $task->code = self::generateUniqueTaskId();
            } else {
                $task->code = self::generateUniqueTaskId($task->code);
            }
        });

        //        static::deleting(function ($task) {
        //            $task->taskLogs()->delete();
        //            $task->meetings()->delete();
        //        });
    }

    public static function generateUniqueTaskId(string $code = null): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $idLength = 7;

        // 코드가 주어진 경우 중복 체크
        if ($code) {
            $isExists = self::query()->where('code', $code)->exists();
            if (!$isExists) {
                return $code;
            }
        }

        // 새로운 고유 코드 생성
        do {
            $taskId = '';
            for ($i = 0; $i < $idLength; $i++) {
                $taskId .= $characters[random_int(0, strlen($characters) - 1)];
            }
        } while (self::query()->where('code', $taskId)->exists());

        return $taskId;
    }

    public function taskLogs(): HasMany
    {
        return $this->hasMany(TaskLog::class);
    }

    public function taskLogSends(): HasMany
    {
        return $this->hasMany(TaskLogSend::class);
    }

    public function meeting(): HasOne
    {
        return $this->hasOne(Meeting::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function smartGlasses(): BelongsTo
    {
        return $this->belongsTo(SmartGlasses::class);
    }

    public function vimeoVideo(): BelongsTo
    {
        return $this->belongsTo(VimeoVideo::class);
    }
}
