<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Regions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Regions::query();
        $q = $request->query('q');
        if ($q) {
            $query->where('name', 'like', "%{$q}%");
        }

        // Filters: pov and level
        $pov = $request->query('pov');
        if ($pov) {
            $query->where('pov', $pov);
        }
        $level = $request->query('level');
        if ($level) {
            $query->where('level', $level);
        }

        // Sorting: allow client to request sort by column and direction. If no explicit sort
        // is provided and a POV is supplied, keep the specialized type-priority ordering.
        $sort = $request->query('sort');
        $dir = strtolower($request->query('dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        // Allowed sortable keys: id, name, pov, type, code, parent (parent name), level
        $allowed = ['id', 'name', 'pov', 'type', 'code', 'parent', 'level'];

        if ($sort && in_array($sort, $allowed)) {
            // If sorting by parent, join to get parent name
            if ($sort === 'parent') {
                $query = $query->leftJoin('regions as p', 'regions.parent_id', '=', 'p.id')
                    ->select('regions.*')
                    ->orderBy('p.name', $dir);
            } elseif ($sort === 'type') {
                // Order by canonical key then legacy type as fallback
                $query->orderBy('type_key', $dir)->orderBy('type', $dir);
            } elseif ($sort === 'level') {
                // Custom ordering for level: REGIONAL, AREA, WITEL, STO (others last)
                $levelOrder = ['REGIONAL','AREA','WITEL','STO'];
                $case = "CASE ";
                foreach ($levelOrder as $i => $lvl) {
                    $case .= "WHEN level = '".addslashes($lvl)."' THEN {$i} ";
                }
                $case .= "ELSE " . count($levelOrder) . " END";
                $query->orderByRaw($case . ' ' . $dir)->orderBy('name', 'asc');
            } else {
                $query->orderBy($sort, $dir);
            }
            $regions = $query->paginate(20);
        } else {
            if ($pov) {
                // Build a CASE expression based on the priority ordering for the given POV.
                $order = Regions::typeOrderForPov($pov);
                $case = "CASE ";
                foreach ($order as $i => $t) {
                    $case .= "WHEN type_key = '".addslashes($t)."' THEN {$i} ";
                }
                $case .= "ELSE " . count($order) . " END";
                $regions = $query->orderByRaw($case)->orderBy('name')->paginate(20);
            } else {
                $regions = $query->orderBy('name')->paginate(20);
            }
        }

        // Provide available level values for the filter select — use canonical list so AREA always appears
        $levels = 
            defined('\App\Models\Regions::LEVELS') ? \App\Models\Regions::LEVELS : Regions::whereNotNull('level')->distinct()->orderBy('level')->pluck('level')->toArray();

        return view('admin.master.regions.index', compact('regions', 'q', 'pov', 'level', 'sort', 'dir', 'levels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $allParents = Regions::orderBy('name')->get();
        // Prefer filtering parents by level hierarchy rather than by type_key.
        $selectedLevel = old('level') ?? null;
        $parentLevel = Regions::parentLevelFor($selectedLevel);
        if (empty($parentLevel)) {
            // No specific parent level required — expose all parents (top-level selection possible)
            $parents = $allParents;
        } else {
            $parents = $allParents->filter(function($p) use ($parentLevel) {
                return strtoupper($p->level ?? '') === strtoupper($parentLevel);
            })->values();
        }

        return view('admin.master.regions.create', compact('parents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required','string','max:255',
                Rule::unique('regions')->where(function ($query) use ($request) {
                    return $query->where('level', $request->input('level'));
                }),
            ],
            'pov' => 'nullable|string|in:'.implode(',', array_keys(Regions::POVS)),
            'level' => 'required|string|max:50',
            // 'type' and 'type_key' are derived from level now; keep legacy fields writable but not required
            'type' => 'nullable|string|max:50',
            'type_key' => 'nullable|string|max:50',
            'pov_mappings' => 'nullable|array',
            'pov_mappings.*.parent_id' => 'nullable|exists:regions,id',
            'code' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:regions,id',
        ]);

        // Normalize pov: default to TELKOM_OLD unless this is an AREA (AREA -> ALL)
        // Derive canonical type_key from selected level to keep Type in sync with Level
        if (!empty($data['level'])) {
            $data['type_key'] = strtoupper($data['level']);
        }

        if (empty($data['pov'])) {
            $isArea = (!empty($data['type_key']) && $data['type_key'] === 'AREA') || (!empty($data['type']) && strtoupper($data['type']) === 'AREA');
            $data['pov'] = $isArea ? 'ALL' : 'TELKOM_OLD';
        }

        // Normalize legacy `type` to safe legacy enum values only. If we have a canonical type_key,
        // map it back to the legacy enum when possible; otherwise remove `type` so we won't insert invalid enum strings.
        if (!empty($data['type_key'])) {
            $legacy = \App\Models\Regions::typeKeyToLegacy($data['type_key']);
            if ($legacy) {
                $data['type'] = $legacy;
            } else {
                unset($data['type']);
            }
        } else {
            // No canonical provided: if `type` exists but isn't one of known legacy keys, remove it.
            if (!empty($data['type'])) {
                $known = array_keys(\App\Models\Regions::LEGACY_MAP);
                if (!in_array(strtoupper($data['type']), $known)) {
                    unset($data['type']);
                }
            }
        }

        // If DB does not yet have the pov/type_key columns, strip them
        if (! Schema::hasColumn('regions', 'pov')) {
            unset($data['pov']);
        }
        if (! Schema::hasColumn('regions', 'type_key')) {
            unset($data['type_key']);
        }
        // If DB does not yet have pov_mappings column, strip it
        if (! Schema::hasColumn('regions', 'pov_mappings')) {
            unset($data['pov_mappings']);
        }
        // If DB does not yet have level column, strip it
        if (! Schema::hasColumn('regions', 'level')) {
            unset($data['level']);
        }

        // Authorization: ensure the current user can create under the provided parent_id
        $candidate = new Regions($data);
        $this->authorize('create', $candidate);

        try {
            Regions::create($data);
        } catch (QueryException $e) {
            // return a helpful error so the admin knows what's wrong
            return back()->withInput()->withErrors(['db' => 'Database error saving region: '.$e->getMessage()]);
        }
        return redirect()->route('admin.regions.index')->with('success', 'Region created.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Regions $region)
    {
        $allParents = Regions::where('id', '!=', $region->id)->orderBy('name')->get();
        // Determine parent options by level hierarchy: pick parents whose level is one above this region's level
        $selectedLevel = old('level', $region->level ?? null);
        $parentLevel = Regions::parentLevelFor($selectedLevel);
        if (empty($parentLevel)) {
            $parents = $allParents;
        } else {
            $parents = $allParents->filter(function($p) use ($parentLevel) {
                return strtoupper($p->level ?? '') === strtoupper($parentLevel);
            })->values();
        }

        return view('admin.master.regions.edit', ['region' => $region, 'parents' => $parents]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Regions $region)
    {
    $user = Auth::user();
    $userId = $user ? $user->id : null;
        try {
            $data = $request->validate([
            'name' => [
                'required','string','max:255',
                Rule::unique('regions')->where(function ($query) use ($request) {
                    return $query->where('level', $request->input('level'));
                })->ignore($region->id),
            ],
            'pov' => 'nullable|string|in:'.implode(',', array_keys(Regions::POVS)),
            'level' => 'required|string|max:50',
            'type' => 'nullable|string|max:50',
            'type_key' => 'nullable|string|max:50',
            'pov_mappings' => 'nullable|array',
            'pov_mappings.*.parent_id' => 'nullable|exists:regions,id',
            'code' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:regions,id',
            ]);
        } catch (ValidationException $e) {
            // Log validation failures for diagnostic purposes and rethrow so normal behaviour remains
            Log::warning('Region update validation failed', [
                'user_id' => $userId,
                'region_id' => $region->id,
                'payload' => $request->except(['_token', '_method', '_csrf']),
                'errors' => $e->errors(),
            ]);
            throw $e;
        }

        // Derive canonical type_key from selected level so Type is always in sync with Level
        if (!empty($data['level'])) {
            $data['type_key'] = strtoupper($data['level']);
        }

        // Normalize pov: default to TELKOM_OLD unless this is an AREA (AREA -> ALL)
        if (empty($data['pov'])) {
            $isArea = (!empty($data['type_key']) && $data['type_key'] === 'AREA') || (!empty($data['type']) && strtoupper($data['type']) === 'AREA');
            $data['pov'] = $isArea ? 'ALL' : 'TELKOM_OLD';
        }

        // Normalize legacy `type` to safe legacy enum values only. If we have a canonical type_key,
        // map it back to the legacy enum when possible; otherwise remove `type` so we won't insert invalid enum strings.
        if (!empty($data['type_key'])) {
            $legacy = \App\Models\Regions::typeKeyToLegacy($data['type_key']);
            if ($legacy) {
                $data['type'] = $legacy;
            } else {
                unset($data['type']);
            }
        } else {
            // No canonical provided: if `type` exists but isn't one of known legacy keys, remove it.
            if (!empty($data['type'])) {
                $known = array_keys(\App\Models\Regions::LEGACY_MAP);
                if (!in_array(strtoupper($data['type']), $known)) {
                    unset($data['type']);
                }
            }
        }

        // If DB does not yet have the pov/type_key columns, strip them
        if (! Schema::hasColumn('regions', 'pov')) {
            unset($data['pov']);
        }
        if (! Schema::hasColumn('regions', 'type_key')) {
            unset($data['type_key']);
        }
        if (! Schema::hasColumn('regions', 'pov_mappings')) {
            unset($data['pov_mappings']);
        }

        // Log attempt and then authorize/update; catch authorization failures to help diagnose
        $oldLevel = $region->level;
        Log::info('Region update attempt (pre-authorize)', [
            'user_id' => $userId,
            'region_id' => $region->id,
            'payload' => array_filter($data, function($k) { return !in_array($k, ['_token','_method']); }, ARRAY_FILTER_USE_KEY),
            'old_level' => $oldLevel,
        ]);

        try {
            $this->authorize('update', $region);
            // Preserve existing type_key/type if they were not submitted in the form to avoid
            // accidental changes when editing other attributes like `level`.
            if (! array_key_exists('type_key', $data)) {
                $data['type_key'] = $region->type_key;
            }
            if (! array_key_exists('type', $data)) {
                $data['type'] = $region->type;
            }
            $region->update($data);
        } catch (AuthorizationException $e) {
            Log::warning('Region update authorization failed', [
                'user_id' => $userId,
                'region_id' => $region->id,
                'payload' => array_filter($data, function($k) { return !in_array($k, ['_token','_method']); }, ARRAY_FILTER_USE_KEY),
                'message' => $e->getMessage(),
            ]);
            throw $e;
        } catch (QueryException $e) {
            Log::error('Region update DB error', [
                'user_id' => $userId,
                'region_id' => $region->id,
                'payload' => array_filter($data, function($k) { return !in_array($k, ['_token','_method']); }, ARRAY_FILTER_USE_KEY),
                'error' => $e->getMessage(),
            ]);
            return back()->withInput()->withErrors(['db' => 'Database error updating region: '.$e->getMessage()]);
        }

        // Log successful update and show old vs new level
        $region->refresh();
        Log::info('Region updated (post-update)', [
            'user_id' => $userId,
            'region_id' => $region->id,
            'old_level' => $oldLevel,
            'new_level' => $region->level,
            'payload' => array_filter($data, function($k) { return !in_array($k, ['_token','_method']); }, ARRAY_FILTER_USE_KEY),
        ]);
        return redirect()->route('admin.regions.index')->with('success', 'Region updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Regions $region)
    {
        $this->authorize('delete', $region);
        $region->delete();
        return redirect()->route('admin.regions.index')->with('success', 'Region deleted.');
    }
}
