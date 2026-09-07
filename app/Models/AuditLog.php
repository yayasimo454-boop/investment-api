<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['admin_id', 'action', 'target_type', 'target_id', 'old_value', 'new_value'])]
class AuditLog extends Model
{
    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}