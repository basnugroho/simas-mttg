<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mosque;
use App\Models\Activity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MosqueActivityController extends Controller
{
    public function index(Request $request)
    {
        // get mosques in user's allowed scope (simple: all mosques for now, filter by allowedScope if needed)
        $me = $request->user();

        // build a base mosque query. Apply simple scope filtering based on user's assigned regions if available.
        $mosquesQuery = Mosque::query()->orderBy('name');
        try {
            if ($me) {
                $allExpanded = [];
                foreach ($me->regionsRoles()->get() as $ar) {
                    $rid = (int)$ar->region_id;
                    try { $desc = \App\Models\Regions::collectDescendantIds($rid); }
                    catch (\Throwable $_e) { $desc = [$rid]; }
                    $expanded = is_array($desc) ? $desc : (is_callable([$desc, 'toArray']) ? $desc->toArray() : [$rid]);
                    $allExpanded = array_merge($allExpanded, $expanded);
                }
                $allExpanded = array_values(array_unique($allExpanded));
                if (count($allExpanded)) {
                    $mosquesQuery->where(function($q) use($allExpanded){
                        $q->whereIn('regional_id', $allExpanded)
                          ->orWhereIn('area_id', $allExpanded)
                          ->orWhereIn('witel_id', $allExpanded)
                          ->orWhereIn('sto_id', $allExpanded)
                          ->orWhereIn('province_id', $allExpanded)
                          ->orWhereIn('city_id', $allExpanded);
                    });
                }
            }
        } catch (\Throwable $__e) {
            // ignore and fall back to no filter
        }

        $mosques = $mosquesQuery->get();

        $activities = Activity::orderBy('activity_name')->get();

        // selected mosque ids from form (array) or single selection
        $selected = $request->query('mosque_ids') ? (array) $request->query('mosque_ids') : [];

        // Build assignments table with optional filters
        // include activity name, mosque name, multiple mosque region levels and activity creator
        $assignQuery = DB::table('activity_mosque')
            ->select(
                'activity_mosque.*',
                'activities.activity_name',
                'mosques.name as mosque_name',
                'mosques.regional_id', 'mosques.area_id', 'mosques.witel_id', 'mosques.province_id', 'mosques.city_id', 'mosques.sto_id',
                'regional.name as regional_name',
                'area.name as area_name',
                'witel.name as witel_name',
                'province.name as province_name',
                'city.name as city_name',
                'sto.name as sto_name',
                'users.name as activity_creator'
            )
            ->join('activities', 'activities.id', '=', 'activity_mosque.activity_id')
            ->leftJoin('users', 'users.id', '=', 'activities.created_by')
            ->join('mosques', 'mosques.id', '=', 'activity_mosque.mosque_id')
            ->leftJoin('regions as regional', 'regional.id', '=', 'mosques.regional_id')
            ->leftJoin('regions as area', 'area.id', '=', 'mosques.area_id')
            ->leftJoin('regions as witel', 'witel.id', '=', 'mosques.witel_id')
            ->leftJoin('regions as province', 'province.id', '=', 'mosques.province_id')
            ->leftJoin('regions as city', 'city.id', '=', 'mosques.city_id')
            ->leftJoin('regions as sto', 'sto.id', '=', 'mosques.sto_id');

        if ($selected && count($selected)) {
            $assignQuery->whereIn('activity_mosque.mosque_id', $selected);
        }

        // filter by mosque name (partial)
        if ($request->filled('filter_mosque')) {
            $assignQuery->where('mosques.name', 'like', '%' . $request->query('filter_mosque') . '%');
        }

        // filter by date range
        if ($request->filled('date_from')) {
            $assignQuery->whereDate('activity_mosque.created_at', '>=', $request->query('date_from'));
        }
        if ($request->filled('date_to')) {
            $assignQuery->whereDate('activity_mosque.created_at', '<=', $request->query('date_to'));
        }

        $assignments = $assignQuery->orderBy('activity_mosque.created_at', 'desc')->paginate(20)->appends($request->query());

        // Build a left-to-right region path for each assignment (parent -> child)
        $assignments->getCollection()->transform(function ($a) {
            $parts = [];
            // prefer higher-level names first (regional -> area -> province -> city -> witel -> sto)
            if (!empty($a->regional_name)) $parts[] = $a->regional_name;
            if (!empty($a->area_name)) $parts[] = $a->area_name;
            if (!empty($a->province_name)) $parts[] = $a->province_name;
            if (!empty($a->city_name)) $parts[] = $a->city_name;
            if (!empty($a->witel_name)) $parts[] = $a->witel_name;
            if (!empty($a->sto_name)) $parts[] = $a->sto_name;

            $a->region_path = count($parts) ? implode(' / ', $parts) : '-';
            return $a;
        });

        return view('admin.mosque_activities.index', compact('mosques','activities','selected','assignments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'mosque_ids' => 'required|array|min:1',
            'mosque_ids.*' => 'required|exists:mosques,id',
            'note' => 'nullable|string|max:255',
            'event_start' => 'nullable|date',
            'event_end' => 'nullable|date',
            'photos.*' => 'nullable|image|max:5120',
        ]);

        $activityId = $data['activity_id'];
        $note = $data['note'] ?? null;
        $eventStart = $data['event_start'] ?? null;
        $eventEnd = $data['event_end'] ?? null;

        foreach ($data['mosque_ids'] as $mid) {
            try {
                $m = Mosque::findOrFail($mid);

                // check existing assignment
                $existing = DB::table('activity_mosque')->where('activity_id', $activityId)->where('mosque_id', $mid)->first();
                if ($existing) {
                    // update
                    $update = ['note' => $note, 'event_start' => $eventStart, 'event_end' => $eventEnd, 'updated_at' => now()];
                    DB::table('activity_mosque')->where('id', $existing->id)->update($update);
                    $pivotId = $existing->id;
                } else {
                    // insert and get id
                    $pivotId = DB::table('activity_mosque')->insertGetId([
                        'activity_id' => $activityId,
                        'mosque_id' => $mid,
                        'note' => $note,
                        'event_start' => $eventStart,
                        'event_end' => $eventEnd,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // handle uploaded photos (store under storage/app/public/activity_mosque_photos/{pivotId}/)
                if ($request->hasFile('photos')) {
                    foreach ($request->file('photos') as $file) {
                        if (! $file->isValid()) continue;
                        $stored = $file->store("activity_mosque_photos/{$pivotId}", 'public');
                        if ($stored) {
                            DB::table('activity_mosque_photos')->insert([
                                'activity_mosque_id' => $pivotId,
                                'path' => $stored,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }

            } catch (\Throwable $e) {
                continue;
            }
        }

        return redirect()->back()->with('success', 'Activity assigned to selected mosque(s)');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'note' => 'nullable|string|max:255',
        ]);
        DB::table('activity_mosque')->where('id', $id)->update(['note' => $data['note'] ?? null]);
        return redirect()->back()->with('success', 'Assignment updated');
    }

    public function destroy($id)
    {
        DB::table('activity_mosque')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Assignment removed');
    }
}
