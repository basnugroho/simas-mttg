<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Mosque;

class MosquePolicy
{
    /**
     * Determine whether the user can create the mosque.
     * Allow if user is webmaster or any of the mosque's region fields fall inside user's effective regions.
     */
    public function create(User $user, Mosque $mosque): bool
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
