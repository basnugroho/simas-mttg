<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Regions;

class RegionPolicy
{
    /**
     * Determine whether the user can create a region.
     * Creating a top-level region (no parent_id) requires webmaster.
     * Creating under a parent requires that parent to be within user's effective regions.
     */
    public function create(User $user, Regions $region): bool
    {
        if ($user->isWebmaster()) return true;

        $parentId = $region->parent_id;
        if (empty($parentId)) {
            // Only webmaster may create top-level regions
            return false;
        }

        $effective = $user->getEffectiveRegionIds();
        return in_array((int)$parentId, $effective, true);
    }

    /**
     * Update allowed only when the target region is inside user's effective regions
     */
    public function update(User $user, Regions $region): bool
    {
        if ($user->isWebmaster()) return true;
        $effective = $user->getEffectiveRegionIds();
        return in_array((int)$region->id, $effective, true);
    }

    /**
     * Delete follows same rule as update
     */
    public function delete(User $user, Regions $region): bool
    {
        return $this->update($user, $region);
    }
}
