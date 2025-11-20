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
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class UserManagementController extends Controller
{
    public function index()
    {
        $me = Auth::user();
        if (! $me || (! $me->isWebmaster() && ! $me->isAdmin())) {
            abort(403);
        }

        $regions = Regions::orderBy('name')->get();

        // Build base users query so we can support search, filters and sorting
        $query = User::with('regionsRoles.region');

        // Filters from query string
        $q = request()->query('q'); // search
        $sort = request()->query('sort');
        $dir = strtolower(request()->query('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $filterRole = request()->query('filter_role'); // role_key filter
        $filterRegion = request()->query('filter_region'); // region id filter

        if ($q) {
            $query->where(function($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                   ->orWhere('username', 'like', "%{$q}%")
                   ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($filterRole) {
            $query->whereHas('regionsRoles', function($qq) use ($filterRole) {
                $qq->where('role_key', $filterRole);
            });
        }

        if ($filterRegion) {
            $query->whereHas('regionsRoles', function($qq) use ($filterRegion) {
                $qq->where('region_id', (int)$filterRegion);
            });
        }

        // Allow sorting by specific columns only
        $allowedSorts = ['id','name','username','email','created_at'];
        if ($sort && in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $dir);
        } else {
            $query->orderBy('id', 'desc');
        }

        $users = $query->paginate(30)->appends(request()->query());

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

        // roleHierarchy maps an assigner role -> immediate target roles it may appoint
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

            // From assignerIds, prefer regions at the desired level that are either the assignerIds
            // themselves or direct children of those ids (covers cases like admin_regional -> admin_area -> admin_witel)
            $ids = Regions::where('level', $level)
                ->where(function($q) use ($assignerIds) {
                    $q->whereIn('id', $assignerIds)->orWhereIn('parent_id', $assignerIds);
                })->pluck('id')->map(fn($v) => (int)$v)->toArray();
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

        // roleHierarchy maps an assigner role -> immediate target roles it may appoint
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
            // merge per-role effective ids with overall effective regions as a fallback
            $allEff = $me->getEffectiveRegionIds(null);
            foreach (array_keys($roleHierarchy) as $k) {
                $ids = $me->getEffectiveRegionIds($k);
                if (empty($ids)) $ids = $allEff;
                $assignerEffective[$k] = $ids;
            }
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

        // Only keep regions at the target level inside allowedIds. Also include direct children of assigner ids
        $regions = Regions::where('level', $targetLevel)
            ->where(function($q) use ($allowedIds) {
                $q->whereIn('id', $allowedIds)->orWhereIn('parent_id', $allowedIds);
            })->orderBy('name')->get()->map(fn($r) => ['id' => $r->id, 'label' => $r->name . ' (' . $r->displayTypeLabel() . ')'])->values();

        return response()->json(['all_allowed' => false, 'regions' => $regions]);
    }

    public function update(Request $request, $id)
    {
        $me = Auth::user();
        if (! $me) {
            logger()->warning('UserManagementController@update called without authenticated user', ['id' => $id, 'ip' => $request->ip()]);
            return redirect()->route('admin.users')->with('status', 'Not authenticated. Silakan login ulang.');
        }

        $user = User::findOrFail($id);

        $data = $request->validate([
            'role' => ['required', 'in:user,admin,webmaster'],
            'approved' => ['nullable', 'in:0,1'],
        ]);

        // Changing the account role is restricted to webmaster only
        if (isset($data['role']) && $data['role'] !== $user->role && ! $me->isWebmaster()) {
            logger()->warning('Unauthorized role change attempt', ['assigner' => $me->id, 'target_user' => $user->id, 'from' => $user->role, 'to' => $data['role'], 'ip' => $request->ip()]);
            return redirect()->route('admin.users')->with('status', 'Hanya webmaster yang dapat mengubah peran akun.');
        }

        // Approve/disapprove: webmaster may do it; admins may do it only for users under their scope
        $canToggleApproved = false;
        if ($me->isWebmaster()) {
            $canToggleApproved = true;
        } elseif ($me->isAdmin()) {
            // check if target user has region-role inside assigner's effective regions
            try {
                $eff = $me->getEffectiveRegionIds();
                if (! empty($eff)) {
                    $has = \App\Models\UserRegionRole::where('user_id', $user->id)->whereIn('region_id', $eff)->exists();
                    if ($has) $canToggleApproved = true;
                }
            } catch (\Throwable $e) {
                // if helper not available or fails, deny conservatively and log
                logger()->warning('Error while checking effective regions for update', ['assigner' => $me->id, 'error' => $e->getMessage()]);
                $canToggleApproved = false;
            }
        }

        if (isset($data['approved']) && ! $canToggleApproved) {
            logger()->warning('Unauthorized approved toggle attempt', ['assigner' => $me->id, 'target_user' => $user->id, 'ip' => $request->ip()]);
            return redirect()->route('admin.users')->with('status', 'Anda tidak punya izin untuk mengubah status persetujuan pengguna ini.');
        }

        // At this point changes are permitted
        $user->role = $data['role'];

        // normalize approved value: checkbox may be absent when unchecked
        $newApproved = isset($data['approved']) && $data['approved'] == '1';

        // If approval is being removed, record who unapproved and when. If approved again, clear the audit.
        if (! $newApproved && $user->approved) {
            // transitioning from approved -> unapproved
            try {
                $user->unapproved_by = $me->id;
                $user->unapproved_at = \Carbon\Carbon::now();
            } catch (\Throwable $e) {
                logger()->warning('Failed to set unapproved_by on user update', ['error' => $e->getMessage(), 'assigner' => $me->id, 'target' => $user->id]);
            }
        } elseif ($newApproved) {
            // transitioning to approved: clear previous unapproval audit
            $user->unapproved_by = null;
            $user->unapproved_at = null;
        }

        $user->approved = $newApproved;
        $user->save();

        return redirect()->route('admin.users')->with('status', 'Perubahan disimpan.');
    }

    /**
     * AJAX: toggle approved status for a user and record unapproved_by/unapproved_at when revoked.
     * Returns JSON { success: true, approved: bool, unapproved_by: id|null, unapproved_at: timestamp|null }
     */
    public function toggleApproved(Request $request, $id)
    {
        $me = Auth::user();
        if (! $me) return response()->json(['error' => 'Unauthenticated'], 401);

        $user = User::findOrFail($id);

        $data = $request->validate([
            'approved' => ['required', 'in:0,1'],
        ]);

        // Check permission similarly to update()
        $canToggleApproved = false;
        if ($me->isWebmaster()) {
            $canToggleApproved = true;
        } elseif ($me->isAdmin()) {
            try {
                $eff = $me->getEffectiveRegionIds();
                if (! empty($eff)) {
                    $has = \App\Models\UserRegionRole::where('user_id', $user->id)->whereIn('region_id', $eff)->exists();
                    if ($has) $canToggleApproved = true;
                }
            } catch (\Throwable $e) {
                logger()->warning('Error while checking effective regions for toggleApproved', ['assigner' => $me->id, 'error' => $e->getMessage()]);
                $canToggleApproved = false;
            }
        }

        if (! $canToggleApproved) {
            return response()->json(['error' => 'Unauthorized to change approval state'], 403);
        }

        $newApproved = $data['approved'] == '1';

        if (! $newApproved && $user->approved) {
            // revoking approval
            $user->unapproved_by = $me->id;
            $user->unapproved_at = Carbon::now();
        } elseif ($newApproved) {
            $user->unapproved_by = null;
            $user->unapproved_at = null;
        }

        $user->approved = $newApproved;
        $user->save();

        return response()->json(['success' => true, 'approved' => (bool)$user->approved, 'unapproved_by' => $user->unapproved_by, 'unapproved_at' => $user->unapproved_at]);
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

    /**
     * Show create user form where admin/webmaster can create a user and assign region role.
     */
    public function create()
    {
        $me = Auth::user();
        if (! $me || (! $me->isWebmaster() && ! $me->isAdmin())) abort(403);

        $regions = Regions::orderBy('name')->get();

        // prepare allowedByTargetRole same as index so form can limit selection
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
        foreach (array_keys($targetLevelMap) as $targetRole) {
            $level = $targetLevelMap[$targetRole];
            if ($me->isWebmaster()) {
                $ids = Regions::where('level', $level)->pluck('id')->map(fn($v)=> (int)$v)->toArray();
                $allowedByTargetRole[$targetRole] = $ids; continue;
            }
            $assignerIds = [];
            $allEff = $me->getEffectiveRegionIds(null);
            foreach (array_keys($roleHierarchy) as $assignerRoleKey) {
                if (! in_array($targetRole, $roleHierarchy[$assignerRoleKey] ?? [], true)) continue;
                $eff = $me->getEffectiveRegionIds($assignerRoleKey);
                if (empty($eff)) $eff = $allEff;
                $assignerIds = array_merge($assignerIds, $eff);
            }
            $assignerIds = array_values(array_unique($assignerIds));
            if (empty($assignerIds)) { $allowedByTargetRole[$targetRole] = []; continue; }
            // prefer regions at target level that are either in assignerIds or are direct children of assignerIds
            $ids = Regions::where('level', $level)
                ->where(function($q) use ($assignerIds) {
                    $q->whereIn('id', $assignerIds)->orWhereIn('parent_id', $assignerIds);
                })->pluck('id')->map(fn($v)=>(int)$v)->toArray();
            $allowedByTargetRole[$targetRole] = $ids;
        }

        $allRegions = $regions->mapWithKeys(fn($r) => [$r->id => $r->name . ' (' . $r->displayTypeLabel() . ')'])->toArray();

        // Current assigner info for the UI: role and assigned regions grouped by role_key
        $myRole = $me->role;
        $myAssignments = $me->regionsRoles()->with('region')->get()->map(function($ar){
            return ['role_key' => $ar->role_key, 'region_name' => $ar->region->name ?? null];
        })->groupBy('role_key')->map(function($group){
            return $group->pluck('region_name')->filter()->unique()->values()->toArray();
        })->toArray();

        return view('admin.users_create', compact('regions', 'allowedByTargetRole', 'allRegions', 'myRole', 'myAssignments'));
    }

    /**
     * Store a newly created user and optional region-role assignments.
     */
    public function store(Request $request)
    {
        $me = Auth::user();
        if (! $me || (! $me->isWebmaster() && ! $me->isAdmin())) abort(403);

        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'username' => ['required','string','max:100','unique:users,username'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['nullable','string','min:6'],
            'approved' => ['nullable','in:0,1'],
            'role_key' => ['nullable','in:admin_regional,admin_area,admin_witel,admin_sto'],
            'region_ids' => ['nullable','array'],
            'region_ids.*' => ['integer','exists:regions,id'],
        ]);

    $roleKey = $data['role_key'] ?? null;
    $regionIds = $data['region_ids'] ?? [];
    // normalize region ids to integers to avoid strict type mismatches
    $regionIds = array_map('intval', $regionIds);

        // Authorization: ensure assigner may appoint the target role and the regions
        if ($roleKey) {
            // compute allowed ids for this target role
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

            $targetLevel = $targetLevelMap[$roleKey] ?? null;
            if (! $targetLevel) abort(403);

            $allowedIds = [];
            if ($me->isWebmaster()) {
                $allowedIds = Regions::where('level', $targetLevel)->pluck('id')->map(fn($v)=>(int)$v)->toArray();
            } else {
                $allEff = $me->getEffectiveRegionIds(null);
                foreach (array_keys($roleHierarchy) as $assignerRoleKey) {
                    if (! in_array($roleKey, $roleHierarchy[$assignerRoleKey] ?? [], true)) continue;
                    $ids = $me->getEffectiveRegionIds($assignerRoleKey);
                    if (empty($ids)) $ids = $allEff;
                    $allowedIds = array_merge($allowedIds, $ids);
                }
                $allowedIds = array_values(array_unique($allowedIds));
                // include regions at target level that are either in allowedIds or direct children of allowedIds
                $allowedIds = Regions::where('level', $targetLevel)
                    ->where(function($q) use ($allowedIds) {
                        $q->whereIn('id', $allowedIds)->orWhereIn('parent_id', $allowedIds);
                    })->pluck('id')->map(fn($v)=>(int)$v)->toArray();
            }

            // Ensure provided regionIds are subset of allowedIds
            // If any requested region is outside allowedIds, log full details and reject
            foreach ($regionIds as $rid) {
                if (! in_array($rid, $allowedIds, true)) {
                    // collect human-friendly names for requested ids where possible
                    try {
                        $requestedNames = \App\Models\Regions::whereIn('id', $regionIds)->pluck('name', 'id')->toArray();
                    } catch (\Throwable $e) {
                        $requestedNames = [];
                    }
                    logger()->warning('Assign attempt outside allowed regions', [
                        'assigner' => $me->id,
                        'role_key' => $roleKey,
                        'requested_region_ids' => $regionIds,
                        'requested_region_names' => $requestedNames,
                        'allowed_ids' => $allowedIds,
                        'allowed_count' => count($allowedIds),
                        'ip' => $request->ip(),
                    ]);
                    return redirect()->back()->with('status', 'You are not allowed to assign selected region(s).')->withInput();
                }
            }
        }

        // Create user
        $password = $data['password'] ?? Str::random(12);
        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $password,
            'role' => $roleKey ? 'admin' : 'user',
            'approved' => isset($data['approved']) && $data['approved'] == '1',
        ]);

        // assign regions if provided
        if ($roleKey && !empty($regionIds)) {
            foreach ($regionIds as $rid) {
                \App\Models\UserRegionRole::create([
                    'user_id' => $user->id,
                    'role_key' => $roleKey,
                    'region_id' => (int)$rid,
                    'created_by' => $me->id,
                ]);
            }
        }

        return redirect()->route('admin.users')->with('status', 'User created.');
    }
}
