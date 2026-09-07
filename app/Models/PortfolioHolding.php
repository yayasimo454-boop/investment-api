<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['portfolio_id', 'asset_id', 'quantity', 'average_buy_price'])]
class PortfolioHolding extends Model
{
    protected $casts = [
        'quantity' => 'decimal:8',
        'average_buy_price' => 'decimal:8',
    ];

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}