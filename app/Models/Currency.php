<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'exchange_rate_to_pivot',
    ];

    protected $casts = [
        'exchange_rate_to_pivot' => 'decimal:8',
    ];

    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}