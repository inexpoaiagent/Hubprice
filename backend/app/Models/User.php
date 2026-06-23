<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids, SoftDeletes, HasRoles;

    protected $fillable = [
        'name', 'username', 'email', 'phone', 'password', 'avatar',
        'role', 'is_active', 'is_verified', 'kyc_verified',
        'force_password_change', 'two_factor_enabled', 'two_factor_secret',
        'two_factor_recovery_codes', 'preferred_language', 'preferred_currency',
        'meta', 'last_login_at', 'last_login_ip',
    ];

    protected $hidden = ['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'kyc_verified' => 'boolean',
            'force_password_change' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'meta' => 'array',
        ];
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function kyc()
    {
        return $this->hasOne(KycVerification::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->latest();
    }

    public function favorites()
    {
        return $this->hasMany(ListingFavorite::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    public function isSeller(): bool
    {
        return in_array($this->role, ['seller', 'dealer', 'agency']);
    }
}
