<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipments';
    protected $guarded = [];

    protected $with = ['ship'];

    public function ship(): BelongsTo
    {
        return $this->belongsTo(Ship::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
