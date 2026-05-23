<?php
// =================== TransactionPolicy.php ===================
namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    public function update(User $user, Transaction $transaction): bool
    {
        return $user->couple_id === $transaction->couple_id;
    }

    public function delete(User $user, Transaction $transaction): bool
    {
        // Owner bisa hapus semua, partner hanya miliknya
        if ($user->role === 'owner') return $user->couple_id === $transaction->couple_id;
        return $user->id === $transaction->user_id;
    }
}
