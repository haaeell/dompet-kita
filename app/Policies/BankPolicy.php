<?php

namespace App\Policies;

use App\Models\Bank;
use App\Models\User;

class BankPolicy
{
    public function update(User $user, Bank $bank): bool
    {
        return $user->couple_id === $bank->couple_id;
    }

    public function delete(User $user, Bank $bank): bool
    {
        return $user->couple_id === $bank->couple_id && $user->role === 'owner';
    }
}
