<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['asset_id', 'mu', 'sigma', 'sector', 'market_factor_weight', 'sector_factor_weight'])]
class AssetVolatilityProfile extends Model
{
    protected $casts = [
        'mu' => 'decimal:6',
        'sigma' => 'decimal:6',
        'market_factor_weight' => 'decimal:4',
        'sector_factor_weight' => 'decimal:4',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}