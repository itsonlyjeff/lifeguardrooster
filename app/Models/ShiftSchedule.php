<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ShiftSchedule extends Model
{
    use HasUuids;

    protected $casts = [
        'paid_at' => 'datetime',
        'notification_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_cancelled' => 'boolean',
    ];

    protected function setAmountAttribute($value): void
    {
        $this->attributes['amount'] = $value * 100;
    }

    protected function getAmountAttribute($value): int|float
    {
        return $value / 100;
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

}
