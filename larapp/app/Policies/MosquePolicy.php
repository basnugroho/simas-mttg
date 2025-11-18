<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Mosque;

class MosquePolicy
{
    /**
     * Determine whether the user can create a mosque.
     *
     * Note: Laravel's Gate calls `create` with only the authenticated user (no model instance),
     * so this method must accept a single argument. We allow creation when the user is
     * webmaster or the user has at least one effective region (i.e., they manage some region).
     */
    public function create(User $user): bool
    {
        if ($user->isWebmaster()) return true;

        $regionIds = $user->getEffectiveRegionIds();
        return is_array($regionIds) && count($regionIds) > 0;
    }

    public function update(User $user, Mosque $mosque): bool
    {
        if ($user->isWebmaster()) return true;
        $regionIds = $user->getEffectiveRegionIds();
        $candidates = [
            $mosque->province_id ?? null,
            $mosque->regional_id ?? null,
            $mosque->city_id ?? null,
            $mosque->witel_id ?? null,
            $mosque->sto_id ?? null,
        ];
        foreach ($candidates as $r) {
            if (!empty($r) && in_array((int)$r, $regionIds, true)) return true;
        }
        return false;
    }

    public function delete(User $user, Mosque $mosque): bool
    {
        return $this->update($user, $mosque);
    }
}
