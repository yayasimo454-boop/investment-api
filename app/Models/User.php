<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'phone', 'password', 'role', 'is_fictional', 'badge', 'kyc_status', 'preferred_language', 'is_blocked'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_fictional' => 'boolean',
            'is_blocked' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class);
    }

    public function portfolio(): HasOne
    {
        return $this->hasOne(Portfolio::class);
    }

    public function kycVerifications(): HasMany
    {
        return $this->hasMany(KycVerification::class);
    }

    public function depositRequests(): HasMany
    {
        return $this->hasMany(DepositRequest::class);
    }

    public function withdrawalRequests(): HasMany
    {
        return $this->hasMany(WithdrawalRequest::class);
    }

    public function orderRequests(): HasMany
    {
        return $this->hasMany(OrderRequest::class);
    }
}