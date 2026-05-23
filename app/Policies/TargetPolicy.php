<?php

namespace App\Policies;

use App\Models\Target;
use App\Models\User;

class TargetPolicy
{
    public function delete(User $user, Target $target): bool
    {
        return $user->couple_id === $target->couple_id;
    }
}
