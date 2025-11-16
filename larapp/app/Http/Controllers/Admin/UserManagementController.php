<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Regions;
use App\Models\UserDeletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UserManagementController extends Controller
{
    public function index()
    {
        $me = Auth::user();
        if (! $me || (! $me->isWebmaster() && ! $me->isAdmin())) {
            abort(403);
        }

        $users = User::with('regionsRoles.region')->orderBy('id', 'desc')->paginate(30);
        $regions = Regions::orderBy('name')->get();

        // compute which user ids the current admin can manage (for admins) or all for webmaster
        $manageableUserIds = [];
        if ($me->isWebmaster()) {
            $manageableUserIds = User::pluck('id')->map(fn($v) => (int)$v)->toArray();
        } else {
            // admins can manage users who have assigned region roles inside their effective regions
            $eff = $me->getEffectiveRegionIds();
            if (!empty($eff)) {
                $uids = \App\Models\UserRegionRole::whereIn('region_id', $eff)->pluck('user_id')->unique()->map(fn($v) => (int)$v)->toArray();
                $manageableUserIds = $uids;
            }
        }

        // compute last activity per user from sessions table
        $lastActivities = [];
        try {
            $ids = $users->pluck('id')->toArray();
            if (!empty($ids)) {
                $rows = \DB::table('sessions')->whereIn('user_id', $ids)->selectRaw('user_id, max(last_activity) as last_activity')->groupBy('user_id')->get();
                foreach ($rows as $r) {
                    $lastActivities[$r->user_id] = (int)$r->last_activity;
                }
            }
        } catch (\Throwable $e) {
            // ignore, leave lastActivities empty
        }

        // Prepare allowed region ids per target role for the UI, using the `level` column.
        // Map target role -> required region level
        $targetLevelMap = [
            'admin_regional' => 'REGIONAL',
            'admin_area' => 'AREA',
            'admin_witel' => 'WITEL',
            'admin_sto' => 'STO',
        ];

        $roleHierarchy = [
            'admin_regional' => ['admin_area'],
            'admin_area' => ['admin_witel'],
            'admin_witel' => ['admin_sto'],
        ];

        $allowedByTargetRole = [];
        $allRegions = $regions->mapWithKeys(fn($r) => [$r->id => $r->name . ' (' . $r->displayTypeLabel() . ')'])->toArray();

        foreach (array_keys($targetLevelMap) as $targetRole) {
            $level = $targetLevelMap[$targetRole];
            if ($me->isWebmaster()) {
                // webmaster can choose any region of that level
                $ids = Regions::where('level', $level)->pluck('id')->map(fn($v) => (int)$v)->toArray();
                $allowedByTargetRole[$targetRole] = $ids;
                continue;
            }

            // compute union of assigner effective ids for assigner roles that can appoint this target
            $assignerIds = [];
            foreach (array_keys($roleHierarchy) as $assignerRoleKey) {
                if (! in_array($targetRole, $roleHierarchy[$assignerRoleKey] ?? [], true)) continue;
                $eff = $me->getEffectiveRegionIds($assignerRoleKey);
                $assignerIds = array_merge($assignerIds, $eff);
            }
            $assignerIds = array_values(array_unique($assignerIds));

            if (empty($assignerIds)) {
                $allowedByTargetRole[$targetRole] = [];
                continue;
            }

            // From assignerIds, only keep regions that are at the desired level
            $ids = Regions::whereIn('id', $assignerIds)->where('level', $level)->pluck('id')->map(fn($v) => (int)$v)->toArray();
            $allowedByTargetRole[$targetRole] = $ids;
        }

    return view('admin.users', compact('users', 'regions', 'allowedByTargetRole', 'allRegions', 'manageableUserIds', 'lastActivities'));
    }

    /**
     * AJAX: return allowed regions (id + label) for a given target role based on current assigner scope.
     * Query param: role=admin_area|admin_witel|admin_sto
     */
    public function allowedRegions(Request $request)
    {
        $me = Auth::user();
        if (! $me) return response()->json(['error' => 'Unauthenticated'], 401);

        $target = $request->query('role');
        // Accept admin_regional as a target as well (webmaster-only assignment)
        if (! in_array($target, ['admin_regional', 'admin_area', 'admin_witel', 'admin_sto'], true)) {
            return response()->json(['error' => 'Invalid role'], 400);
        }

        $roleHierarchy = [
            'admin_regional' => ['admin_area'],
            'admin_area' => ['admin_witel'],
            'admin_witel' => ['admin_sto'],
        ];

        // Gather assigner effective ids for each assigner role
        $assignerEffective = [];
        if ($me->isWebmaster()) {
            foreach (array_keys($roleHierarchy) as $k) $assignerEffective[$k] = null;
        } else {
            foreach (array_keys($roleHierarchy) as $k) $assignerEffective[$k] = $me->getEffectiveRegionIds($k);
        }

        // Special-case: admin_regional is only appointed by webmaster. We will return
        // regions that represent "regional" level. Try level='REGIONAL' first, otherwise
        // fall back to a heuristic by type_key.
        if ($target === 'admin_regional') {
            if (! $me->isWebmaster()) {
                // Non-webmaster cannot assign admin_regional — return empty set to the UI
                return response()->json(['all_allowed' => false, 'regions' => []]);
            }

            // Try to use level column if present
            try {
                $byLevel = Regions::where('level', 'REGIONAL')->orderBy('name')->get();
                if ($byLevel->count() > 0) {
                    $regs = $byLevel->map(fn($r) => ['id' => $r->id, 'label' => $r->name . ' (' . $r->displayTypeLabel() . ')'])->values();
                    return response()->json(['all_allowed' => false, 'regions' => $regs]);
                }
            } catch (\Throwable $e) {
                // ignore and fall back
            }

            // Fallback: return regions that have a likely type_key for regional (AREA/TREG)
            $candidates = ['AREA', 'TREG', 'TREG_OLD'];
            $byType = Regions::whereIn('type_key', $candidates)->orderBy('name')->get();
            $regs = $byType->map(fn($r) => ['id' => $r->id, 'label' => $r->name . ' (' . $r->displayTypeLabel() . ')'])->values();
            return response()->json(['all_allowed' => false, 'regions' => $regs]);
        }

        // Map target->level and then compute allowed target-level regions inside assigner's scope
        $targetLevelMap = [
            'admin_area' => 'AREA',
            'admin_witel' => 'WITEL',
            'admin_sto' => 'STO',
        ];

        $targetLevel = $targetLevelMap[$target] ?? null;
        if (! $targetLevel) {
            return response()->json(['all_allowed' => false, 'regions' => []]);
        }

        $allowedIds = [];
        foreach ($assignerEffective as $assignerRoleKey => $ids) {
            if (! in_array($target, $roleHierarchy[$assignerRoleKey] ?? [], true)) continue;
            if (is_null($ids)) {
                // webmaster: return all regions of the target level
                $all = Regions::where('level', $targetLevel)->orderBy('name')->get()->map(fn($r) => ['id' => $r->id, 'label' => $r->name . ' (' . $r->displayTypeLabel() . ')'])->values();
                return response()->json(['all_allowed' => true, 'regions' => $all]);
            }
            $allowedIds = array_merge($allowedIds, $ids);
        }

        $allowedIds = array_values(array_unique($allowedIds));
        if (empty($allowedIds)) {
            return response()->json(['all_allowed' => false, 'regions' => []]);
        }

        // Only keep regions at the target level inside allowedIds
        $regions = Regions::whereIn('id', $allowedIds)->where('level', $targetLevel)->orderBy('name')->get()->map(fn($r) => ['id' => $r->id, 'label' => $r->name . ' (' . $r->displayTypeLabel() . ')'])->values();

        return response()->json(['all_allowed' => false, 'regions' => $regions]);
    }

    public function update(Request $request, $id)
    {
        $me = Auth::user();
        if (! $me || ! $me->isWebmaster()) {
            abort(403);
        }

        $user = User::findOrFail($id);

        $data = $request->validate([
            'role' => ['required', 'in:user,admin,webmaster'],
            'approved' => ['nullable', 'in:0,1'],
        ]);

        $user->role = $data['role'];
        $user->approved = isset($data['approved']) && $data['approved'] == '1';
        $user->save();

        return redirect()->route('admin.users')->with('status', 'Perubahan disimpan.');
    }

    /**
     * Bulk delete selected users. Only webmaster can delete any user. Admins may delete
     * users that are under their effective region scope (enforced here).
     */
    public function bulkDelete(Request $request)
    {
        $me = Auth::user();
        if (! $me || (! $me->isWebmaster() && ! $me->isAdmin())) {
            abort(403);
        }

        $data = $request->validate([
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['integer'],
        ]);

        $ids = array_map('intval', $data['user_ids']);
        if (empty($ids)) {
            return redirect()->route('admin.users')->with('status', 'No users selected.');
        }

        if ($me->isWebmaster()) {
            // webmaster can delete any user except themselves
            $toDelete = array_values(array_diff($ids, [$me->id]));
        } else {
            // admins: only allow deletion of users that have region roles inside admin effective regions
            $eff = $me->getEffectiveRegionIds();
            if (empty($eff)) {
                return redirect()->route('admin.users')->with('status', 'You have no users under your scope.');
            }
            $allowedUserIds = \App\Models\UserRegionRole::whereIn('region_id', $eff)->whereIn('user_id', $ids)->pluck('user_id')->unique()->map(fn($v) => (int)$v)->toArray();
            $toDelete = array_values(array_diff($allowedUserIds, [$me->id]));
        }

        if (!empty($toDelete)) {
            // Record audit entries and perform deletions in transaction
            DB::transaction(function() use ($toDelete, $me) {
                $rows = User::whereIn('id', $toDelete)->get();
                foreach ($rows as $u) {
                    // store payload snapshot
                    try {
                        UserDeletion::create([
                            'deleted_user_id' => $u->id,
                            'deleted_by_user_id' => $me->id,
                            'payload' => $u->toArray(),
                            'deleted_at' => Carbon::now(),
                        ]);
                    } catch (\Throwable $e) {
                        // log failure to record audit but continue with deletion
                        Log::warning('Failed to record user deletion audit', ['user_id' => $u->id, 'error' => $e->getMessage()]);
                    }
                }

                // perform delete
                User::whereIn('id', $toDelete)->delete();
                Log::info('Users deleted via admin.bulkDelete', ['deleted_by' => $me->id, 'deleted_user_ids' => $toDelete]);
            });

            return redirect()->route('admin.users')->with('status', count($toDelete) . ' users deleted.');
        }

        return redirect()->route('admin.users')->with('status', 'No permitted users selected for deletion.');
    }

    /**
     * Delete a single user (soft-delete) with audit. Uses same authorization rules as bulkDelete.
     */
    public function deleteSingle(Request $request, $id)
    {
        $me = Auth::user();
        if (! $me || (! $me->isWebmaster() && ! $me->isAdmin())) {
            abort(403);
        }

        $uid = (int) $id;
        if ($uid === $me->id) {
            return redirect()->route('admin.users')->with('status', 'Cannot delete yourself.');
        }

        // Check authorization for admins
        if (! $me->isWebmaster()) {
            $eff = $me->getEffectiveRegionIds();
            if (empty($eff)) {
                return redirect()->route('admin.users')->with('status', 'You have no users under your scope.');
            }
            $allowed = \App\Models\UserRegionRole::where('user_id', $uid)->whereIn('region_id', $eff)->exists();
            if (! $allowed) {
                return redirect()->route('admin.users')->with('status', 'You are not allowed to delete this user.');
            }
        }

        $user = User::withTrashed()->find($uid);
        if (! $user) return redirect()->route('admin.users')->with('status', 'User not found.');

        DB::transaction(function() use ($user, $me) {
            try {
                UserDeletion::create([
                    'deleted_user_id' => $user->id,
                    'deleted_by_user_id' => $me->id,
                    'payload' => $user->toArray(),
                    'deleted_at' => Carbon::now(),
                ]);
            } catch (\Throwable $e) {
                Log::warning('Failed to record user deletion audit', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            }

            // soft-delete
            $user->delete();
            Log::info('User deleted via admin.deleteSingle', ['deleted_by' => $me->id, 'deleted_user_id' => $user->id]);
        });

        return redirect()->route('admin.users')->with('status', 'User deleted.');
    }
}
