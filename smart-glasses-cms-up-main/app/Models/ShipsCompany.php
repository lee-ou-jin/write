<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class ShipsCompany extends Model
{
    use HasFactory;

    protected $table = 'ships_companies';

    protected $guarded = [];

    public function users(): MorphMany
    {
        return $this->morphMany(User::class, 'roleable');
    }

    public function ships(): HasMany
    {
        return $this->hasMany(Ship::class);
    }
}
