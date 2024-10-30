<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
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
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return str_ends_with($this->email, '@gmail.com') && $this->hasVerifiedEmail();
    }

    public function setIbanAttribute($value): void
    {
        $this->attributes['iban'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getIbanAttribute($value): string|false
    {
        if ($value === null) {
            return false;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return false;
        }
    }
}
