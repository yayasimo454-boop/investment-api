<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'asset_id', 'type', 'quantity', 'requested_price', 'status', 'reviewed_by'])]
class OrderRequest extends Model
{
    protected $casts = [
        'quantity' => 'decimal:8',
        'requested_price' => 'decimal:8',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}