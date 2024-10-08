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
    public function delete(User $user): bool
    {
        $havepPrmissons = $user->hasRole('admin|su|scientist');
        return $havepPrmissons;
    }
}