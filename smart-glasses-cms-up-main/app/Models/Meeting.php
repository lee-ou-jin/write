<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\FcmService;

class Meeting extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($meeting) {
            $meeting->start_time = now();
        });

        static::created(function ($meeting) {

            logger('MEETING CREATED START', [
                'meeting_id' => $meeting->id,
                'task_id' => $meeting->task_id,
            ]);

            $meeting->load('task.smartGlasses');

            $smartGlass = $meeting->task?->smartGlasses;

            logger('SMART GLASS CHECK', [
                'smart_glass_id' => $smartGlass?->id,
                'auth_code' => $smartGlass?->auth_code,
                'fcm_token' => $smartGlass?->fcm_token,
            ]);

            if (!$smartGlass || empty($smartGlass->fcm_token)) {

                logger('MEETING PUSH SKIPPED', [
                    'meeting_id' => $meeting->id,
                    'task_id' => $meeting->task_id,
                    'reason' => 'smart glass or fcm token missing',
                ]);

                return;
            }

            $sent = app(FcmService::class)->sendToToken(
                $smartGlass->fcm_token,
                '새 미팅 생성',
                "{$meeting->title} 미팅이 생성되었습니다.",
                [
                    'type' => 'meeting_created',
                    'meeting_id' => (string) $meeting->id,
                    'task_id' => (string) $meeting->task_id,
                ]
            );

            logger('FCM SEND RESULT', [
                'meeting_id' => $meeting->id,
                'sent' => $sent,
            ]);
        });
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'meeting_users',
            'meeting_id',
            'user_id'
        )->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
