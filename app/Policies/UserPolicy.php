<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{

    public function view(User $user, User $model): bool
    {
        return $user->id === $model->id ||
            $user->role_id !== 3;
    }

    public function modify(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }
}
