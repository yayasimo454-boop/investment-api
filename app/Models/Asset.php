<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['symbol', 'name', 'type', 'sector', 'currency_id', 'is_active'])]
class Asset extends Model
{
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(AssetPrice::class);
    }

    public function volatilityProfile(): HasOne
    {
        return $this->hasOne(AssetVolatilityProfile::class);
    }

    public function latestPrice(): HasOne
    {
        return $this->hasOne(AssetPrice::class)->latestOfMany('recorded_at');
    }
}