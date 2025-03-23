<?php

namespace App\Policies;

use App\Models\Seat;
use App\Models\User;

class SeatPolicy
{
    public function delete(User $user, Seat $seat): bool
    {
        return $user->id === $seat->user_id || $user->role->id !== 3;
    }
}
