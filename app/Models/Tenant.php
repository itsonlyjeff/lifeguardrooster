<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasUuids;

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('is_active', 'is_admin')->withTimestamps();
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }

    public function shiftTypes(): HasMany
    {
        return $this->hasMany(ShiftType::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function shiftTemplates(): HasMany
    {
        return $this->hasMany(ShiftTemplate::class);
    }

    public function shiftTemplateSchedules(): HasMany
    {
        return $this->hasMany(ShiftTemplateSchedules::class);
    }
}
