<?php

namespace App\Policies;

use App\Models\Shift;
use App\Models\User;

class ShiftPolicy
{
    public function view(User $user, Shift $shift): bool
    {
        return $user->departments->contains('id', $shift->department_id);
    }
}
