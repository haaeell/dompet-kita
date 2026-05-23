<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    public function update(User $user, Category $category): bool
    {
        return $user->couple_id === $category->couple_id;
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->couple_id === $category->couple_id && !$category->is_default;
    }
}
