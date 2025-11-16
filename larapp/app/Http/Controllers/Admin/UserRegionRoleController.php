<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRegionRole;
use App\Models\Regions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UserRegionRoleController extends Controller
{
    // Allowed role keys that can be assigned via this controller
    protected $allowedRoles = ['admin_regional', 'admin_area', 'admin_witel', 'admin_sto'];

    public function store(Request $request, $userId)
    {
        $assigner = Auth::user();
        if (! $assigner) abort(403);

        $user = User::findOrFail($userId);

        $data = $request->validate([
            'role_key' => ['required', 'string'],
            'region_ids' => ['required', 'array'],
            'region_ids.*' => ['required', 'integer', 'exists:regions,id'],
        ]);

        $role = $data['role_key'];
        if (! in_array($role, $this->allowedRoles)) {
            return back()->with('status', 'Invalid role');
        }

        // Determine permission using role-hierarchy and per-role effective regions.
        // Webmaster may assign anywhere.
        if ($assigner->isWebmaster()) {
            $canAssignAnywhere = true;
        } else {
            $canAssignAnywhere = false;
        }

        // Hierarchy map: assigner role => list of roles they may appoint under their scope
        $hierarchy = [
            'admin_regional' => ['admin_area'],
            'admin_area' => ['admin_witel'],
            'admin_witel' => ['admin_sto'],
        ];

        // Build assigner's effective regions per role key (only for keys in hierarchy)
        $assignerRoleEffective = [];
        foreach (array_keys($hierarchy) as $rKey) {
            $assignerRoleEffective[$rKey] = $assigner->getEffectiveRegionIds($rKey);
        }

        // For each requested target region, ensure the assigner is allowed to appoint the requested role
        foreach ($data['region_ids'] as $rid) {
            if ($canAssignAnywhere) continue;

            $allowedForThisRegion = false;

            // Try each assigner role: if that assigner role can appoint the requested role and
            // the target region is inside assigner's effective set for that assigner role, allow it.
            foreach ($assignerRoleEffective as $assignerRoleKey => $effIds) {
                if (! isset($hierarchy[$assignerRoleKey])) continue;
                if (! in_array($role, $hierarchy[$assignerRoleKey], true)) continue;

                if (is_array($effIds) && in_array((int)$rid, $effIds, true)) {
                    $allowedForThisRegion = true;
                    break;
                }
            }

            if (! $allowedForThisRegion) {
                return back()->with('status', 'You are not authorized to assign role '.$role.' for region id '.$rid);
            }
        }

        // Persist assignments (idempotent)
        foreach ($data['region_ids'] as $rid) {
            UserRegionRole::updateOrCreate(
                ['user_id' => $user->id, 'role_key' => $role, 'region_id' => $rid],
                ['created_by' => $assigner->id]
            );
        }

        // Invalidate caches for the affected user (and assigner) so effective region lists refresh
        Cache::forget("user:{$user->id}:effective_regions:all");
        Cache::forget("user:{$user->id}:effective_regions:{$role}");
        Cache::forget("user:{$assigner->id}:effective_regions:all");

        return back()->with('status', 'Assignments saved.');
    }

    public function destroy(Request $request, $id)
    {
        $assigner = Auth::user();
        if (! $assigner) abort(403);

        $assignment = UserRegionRole::findOrFail($id);
        // Enforce removal rules: allow webmaster; allow creator; otherwise require assigner to have
        // a parent role that can appoint this assignment->role_key and have the region in their effective set.
        if ($assigner->isWebmaster() || $assignment->created_by === $assigner->id) {
            // allowed
        } else {
            // hierarchy map same as store
            $hierarchy = [
                'admin_regional' => ['admin_area'],
                'admin_area' => ['admin_witel'],
                'admin_witel' => ['admin_sto'],
            ];

            $allowed = false;
            foreach (array_keys($hierarchy) as $assignerRoleKey) {
                if (! in_array($assignment->role_key, $hierarchy[$assignerRoleKey] ?? [], true)) continue;
                $eff = $assigner->getEffectiveRegionIds($assignerRoleKey);
                if (is_array($eff) && in_array((int)$assignment->region_id, $eff, true)) {
                    $allowed = true;
                    break;
                }
            }
            if (! $allowed) abort(403);
        }

        $assignment->delete();
        // Invalidate caches for affected user(s)
        Cache::forget("user:{$assignment->user_id}:effective_regions:all");
        Cache::forget("user:{$assignment->user_id}:effective_regions:{$assignment->role_key}");

        return back()->with('status', 'Assignment removed.');
    }
}
