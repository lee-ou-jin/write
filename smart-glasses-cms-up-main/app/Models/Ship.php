<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ship extends Model
{
    use HasFactory;

//    public function equipments(): HasMany
//    {
//        return $this->hasMany(Equipment::class);
//    }

    public function shipsCompany(): BelongsTo
    {
        return $this->belongsTo(ShipsCompany::class);
    }
}
