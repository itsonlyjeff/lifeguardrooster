<?php

namespace App\Models;

use Filament\Facades\Filament;
use Filament\Models\Contracts\HasTenants;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Collection;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail, HasTenants
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_sys_admin' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
//        dd($panel);
        if ($panel->getId() === 'app')
        {
            return true;
        }

        if ($panel->getId() === 'admin')
        {
            return $this->tenants()->wherePivot('is_admin', true)->exists();
        }

        if($panel->getId() === 'commandcentre')
        {
            return $this->is_sys_admin;
        }

        return false;
    }

    public function setIbanAttribute($value): void
    {
        $this->attributes['iban'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getIbanAttribute($value): string|false
    {
        if ($value === null) {
            return '';
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return false;
        }
    }

    public function getMaskedIbanAttribute(): string
    {
        $iban = $this->iban; //

        if ($iban === null || strlen($iban) <= 10) {
            return $iban;
        }

        return substr($iban, 0, 10).str_repeat('*', strlen($iban) - 10);
    }

    public function tenant(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class);
    }

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class)->withPivot('is_active', 'is_admin')->withTimestamps();
    }

    public function shiftschedules(): HasMany
    {
        return $this->hasMany(Shiftschedule::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'department_user')->withTimestamps();
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    public function getTenants(Panel $panel): Collection
    {
        return $this->tenants;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->tenants()->whereKey($tenant)->exists();
    }


}
