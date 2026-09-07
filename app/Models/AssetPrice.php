<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['asset_id', 'price', 'recorded_at'])]
class AssetPrice extends Model
{
    public $timestamps = false; // cette table n'a que recorded_at, pas created_at/updated_at

    protected $casts = [
        'price' => 'decimal:8',
        'recorded_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}