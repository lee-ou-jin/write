<?php

namespace App\Models;

use App\Events\TaskLogCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($taskLog) {
            if (blank($taskLog->image)) {
                return false;
            }

            event(new TaskLogCreated($taskLog));
        });

        parent::booted();
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
