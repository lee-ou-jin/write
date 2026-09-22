<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskLogSend extends Model
{
    protected $table = 'task_log_send';

    protected $fillable = [
        'task_id',
        'image',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
