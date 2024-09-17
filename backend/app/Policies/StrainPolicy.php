<?php

namespace App\Policies;

use App\Models\Strain;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class StrainPolicy
{
    public function create(User $user): bool
    {
        $havepPrmissons = $user->hasRole('admin|su|scientist');
        return $havepPrmissons;
    }

    public function update(User $user): bool
    {
        $havepPrmissons = $user->hasRole('admin|su|scientist');
        return $havepPrmissons;
    }

    public function changeStatus(User $user, Blog $blog): bool
    {
        return $user->hasRole('admin|moderator|su');
    }

    public function delete(User $user, Blog $blog): bool
    {
        $isAdminOrModerator = $user->hasRole('admin|moderator|su');
        $isAuthorAndBlogger = $user->hasRole('blogger') && $user->id === $blog->author_id;

        return $isAdminOrModerator || $isAuthorAndBlogger;
    }
}