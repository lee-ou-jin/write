<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class EquipmentCompany extends Model
{
    use HasFactory;

    protected $table = 'equipment_companies';

    protected $guarded = [];

    public function users(): MorphMany
    {
        return $this->morphMany(User::class, 'roleable');
    }

    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class);
    }
}
