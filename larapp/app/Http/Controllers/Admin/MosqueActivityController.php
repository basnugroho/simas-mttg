<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mosque;
use App\Models\Activity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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

        // Load photos for the assignments in one query and attach to each assignment
        $assignmentIds = $assignments->getCollection()->pluck('id')->filter()->all();
        if (count($assignmentIds)) {
            $photoRows = DB::table('activity_mosque_photos')->whereIn('activity_mosque_id', $assignmentIds)->orderBy('sort_order')->get()->groupBy('activity_mosque_id');
            $assignments->getCollection()->transform(function ($a) use ($photoRows) {
                $a->photos = isset($photoRows[$a->id]) ? $photoRows[$a->id]->map(function($r){ return (array)$r; })->values()->all() : [];
                return $a;
            });
        } else {
            $assignments->getCollection()->transform(function ($a) { $a->photos = []; return $a; });
        }

        // determine selected mosque (for single-mosque view)
        $selected_mosque = null;
        $selectedId = null;
        if ($request->filled('mosque')) {
            $selectedId = (int) $request->query('mosque');
        } elseif ($selected && count($selected) === 1) {
            $selectedId = (int) $selected[0];
        }
        if ($selectedId) {
            $selected_mosque = Mosque::find($selectedId);
        }

        // optional: prefill edit form when ?edit_assignment={id} provided
        $editingAssignment = null;
        if ($request->filled('edit_assignment')) {
            try {
                $eid = (int) $request->query('edit_assignment');
                $editingAssignment = DB::table('activity_mosque')->where('id', $eid)->first();
                if($editingAssignment){
                    $photos = DB::table('activity_mosque_photos')->where('activity_mosque_id', $eid)->orderBy('sort_order')->get()->map(function($r){ return (array)$r; })->values()->all();
                    $editingAssignment->photos = $photos;
                    // ensure rutin_days is available as array if stored as json
                    if(!empty($editingAssignment->rutin_days) && is_string($editingAssignment->rutin_days)){
                        $decoded = json_decode($editingAssignment->rutin_days, true);
                        if(is_array($decoded)) $editingAssignment->rutin_days = $decoded;
                    }
                }
            } catch (\Throwable $__e) {
                $editingAssignment = null;
            }
        }

        return view('admin.mosque_activities.index', compact('mosques','activities','selected','assignments','editingAssignment','selected_mosque'));
    }

    /**
     * Show the create form.
     */
    public function create(Request $request)
    {
        $mosques = DB::table('mosques')->orderBy('name')->get();
        $activities = Activity::orderBy('activity_name')->get();

        // accept either ?mosque=ID or ?mosque_ids[]=ID (master list uses mosque_ids[] link)
        $selected_mosque = null;
        if ($request->has('mosque')) {
            $selected_mosque = Mosque::find((int)$request->query('mosque'));
        } elseif ($request->query('mosque_ids')) {
            $ids = (array) $request->query('mosque_ids');
            if (count($ids)) {
                $selected_mosque = Mosque::find((int)$ids[0]);
            }
        }

        return view('admin.mosque_activities.create', compact('mosques', 'selected_mosque','activities'));
    }

    /**
     * Show the edit form for an assignment.
     */
    public function edit($id)
    {
        $editing = DB::table('activity_mosque')->where('id', $id)->first();
        if (! $editing) {
            return redirect()->route('admin.mosque_activities.index')->with('error', 'Aktivitas tidak ditemukan.');
        }

        // hydrate arrays and photos
        $editing->rutin_days_array = !empty($editing->rutin_days) && is_string($editing->rutin_days) ? json_decode($editing->rutin_days, true) : [];
        // older schema stores single mosque_id; convert to array for the multi-select form
        $editing->mosque_ids_array = [];
        if (isset($editing->mosque_id) && $editing->mosque_id) {
            $editing->mosque_ids_array = [(int)$editing->mosque_id];
        }
        $editing->photos = DB::table('activity_mosque_photos')->where('activity_mosque_id', $editing->id)->get();

        $mosques = DB::table('mosques')->orderBy('name')->get();
        $activities = Activity::orderBy('activity_name')->get();

        // provide selected_mosque so the form hides the mosque selector for edit
        $selected_mosque = null;
        if (isset($editing->mosque_id) && $editing->mosque_id) {
            $selected_mosque = Mosque::find((int)$editing->mosque_id);
        } elseif (!empty($editing->mosque_ids_array) && is_array($editing->mosque_ids_array) && count($editing->mosque_ids_array)) {
            $selected_mosque = Mosque::find((int)$editing->mosque_ids_array[0]);
        }

        return view('admin.mosque_activities.edit', compact('editing', 'mosques','activities','selected_mosque'));
    }

    public function store(Request $request)
    {
        Log::info('MosqueActivity store called', ['user' => $request->user()?->id, 'activity_id' => $request->input('activity_id'), 'mosque_ids' => $request->input('mosque_ids')]);
        $data = $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'mosque_ids' => 'required|array|min:1',
            'mosque_ids.*' => 'required|exists:mosques,id',
            'note' => 'nullable|string|max:1000',
            'event_start' => 'nullable|date',
            'event_end' => 'nullable|date',
            'is_rutin' => 'nullable|boolean',
            'rutin_days' => 'nullable|array',
            'rutin_days.*' => 'nullable|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            'photos.*' => 'nullable|image|max:5120',
            'photo_captions.*' => 'nullable|string|max:255',
        ]);

        $activityId = $data['activity_id'];
        $note = $data['note'] ?? null;
        $eventStart = $data['event_start'] ?? null;
        $eventEnd = $data['event_end'] ?? null;
        $isRutin = !empty($data['is_rutin']) ? 1 : 0;
        $rutinDays = !empty($data['rutin_days']) ? json_encode(array_values($data['rutin_days'])) : null;

        foreach ($data['mosque_ids'] as $mid) {
            try {
                $m = Mosque::findOrFail($mid);

                // check existing assignment
                $existing = DB::table('activity_mosque')->where('activity_id', $activityId)->where('mosque_id', $mid)->first();
                if ($existing) {
                    // update
                    $update = ['note' => $note, 'event_start' => $eventStart, 'event_end' => $eventEnd, 'is_rutin' => $isRutin, 'rutin_days' => $rutinDays, 'updated_at' => now()];
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
                        'is_rutin' => $isRutin,
                        'rutin_days' => $rutinDays,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // handle uploaded photos (store under storage/app/public/activity_mosque_photos/{pivotId}/)
                if ($request->hasFile('photos')) {
                    $captions = $request->input('photo_captions', []);
                    foreach ($request->file('photos') as $idx => $file) {
                        if (! $file->isValid()) continue;
                        $stored = $file->store("activity_mosque_photos/{$pivotId}", 'public');
                        $caption = isset($captions[$idx]) ? trim($captions[$idx]) : null;
                        if ($stored) {
                            DB::table('activity_mosque_photos')->insert([
                                'activity_mosque_id' => $pivotId,
                                'path' => $stored,
                                'caption' => $caption,
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

        // After creating assignments, redirect back to the activities list.
        // If only one mosque was targeted, return to the filtered list for that mosque.
        if (isset($data['mosque_ids']) && is_array($data['mosque_ids']) && count($data['mosque_ids']) === 1) {
            $mid = (int) $data['mosque_ids'][0];
            $mosqueName = Mosque::where('id', $mid)->value('name') ?? ('#' . $mid);
            return redirect()->route('admin.mosque_activities.index', ['mosque_ids' => [$mid]])->with('success', "Aktivitas untuk {$mosqueName} berhasil dibuat.");
        }

        return redirect()->route('admin.mosque_activities.index')->with('success', 'Activity assigned to selected mosque(s)');
    }

    public function update(Request $request, $id)
    {
                Log::info('MosqueActivity update called', ['user' => $request->user()?->id, 'id' => $id, 'mosque_id' => $request->input('mosque_id')]);
                $data = $request->validate([
                        'note' => 'nullable|string|max:255',
                        'is_rutin' => 'nullable|boolean',
                        'rutin_days' => 'nullable|array',
                        'rutin_days.*' => 'nullable|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
                        'photos.*' => 'nullable|image|max:5120',
                ]);

                $update = [
                        'note' => $data['note'] ?? null,
                        'is_rutin' => !empty($data['is_rutin']) ? 1 : 0,
                        'rutin_days' => !empty($data['rutin_days']) ? json_encode(array_values($data['rutin_days'])) : null,
                        'updated_at' => now(),
                ];

                DB::table('activity_mosque')->where('id', $id)->update($update);

                // handle uploaded photos when editing (append new photos)
                try{
                    if($request->hasFile('photos')){
                        $captions = $request->input('photo_captions', []);
                        foreach($request->file('photos') as $idx => $file){
                            if(!$file->isValid()) continue;
                            $stored = $file->store("activity_mosque_photos/{$id}", 'public');
                            $caption = isset($captions[$idx]) ? trim($captions[$idx]) : null;
                            if($stored){
                                DB::table('activity_mosque_photos')->insert([
                                    'activity_mosque_id' => $id,
                                    'path' => $stored,
                                    'caption' => $caption,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                        }
                    }
                }catch(\Throwable $__e){ /* ignore photo save failures to avoid breaking update */ }

                // find mosque_id for this assignment so we can redirect to the filtered list for that mosque
                $mosqueId = DB::table('activity_mosque')->where('id', $id)->value('mosque_id');
                if($mosqueId){
                        $mosqueName = Mosque::where('id', $mosqueId)->value('name') ?? ('#' . $mosqueId);
                        $msg = 'Aktivitas ' . $mosqueName . ' Berhasil Diperbarui';
                        return redirect()->route('admin.mosque_activities.index', ['mosque_ids' => [$mosqueId]])->with('success', $msg);
                }

                return redirect()->route('admin.mosque_activities.index')->with('success', 'Aktivitas Berhasil Diperbarui');
    }

    /**
     * Toggle is_active via AJAX
     */
    public function toggleActive(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer|exists:activity_mosque,id',
            'is_active' => 'required|boolean',
        ]);
        DB::table('activity_mosque')->where('id', $data['id'])->update(['is_active' => $data['is_active']]);
        return response()->json(['status' => 'ok']);
    }

    public function destroy($id)
    {
        DB::table('activity_mosque')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Assignment removed');
    }

    /**
     * Delete a photo record and remove its file from disk
     */
    public function destroyPhoto($id)
    {
        $photo = DB::table('activity_mosque_photos')->where('id', $id)->first();
        if (! $photo) {
            if(request()->wantsJson() || request()->ajax()){
                return response()->json(['status'=>'error','message'=>'Foto tidak ditemukan'], 404);
            }
            return redirect()->back()->with('error', 'Foto tidak ditemukan');
        }

        try {
            // delete the file from the public disk
            \Illuminate\Support\Facades\Storage::disk('public')->delete($photo->path);
        } catch (\Throwable $__e) {
            // ignore file delete errors
        }

        DB::table('activity_mosque_photos')->where('id', $id)->delete();
        if(request()->wantsJson() || request()->ajax()){
            return response()->json(['status'=>'ok']);
        }
        return redirect()->back()->with('success', 'Foto dihapus');
    }

    /**
     * Return assignment data as JSON (including photo URLs) for client-side edit prefill
     */
    public function showJson($id)
    {
        $assignment = DB::table('activity_mosque')->where('id', $id)->first();
        if (! $assignment) {
            return response()->json(['success' => false, 'message' => 'Assignment not found'], 404);
        }
        $photos = DB::table('activity_mosque_photos')->where('activity_mosque_id', $id)->orderBy('sort_order')->get()->map(function($r){
            $row = (array) $r;
            $path = $row['path'] ?? null;
            try{
                if($path && (strpos($path, 'http') === 0 || strpos($path, '/') === 0)){
                    $row['url'] = $path;
                } else {
                    $row['url'] = Storage::disk('public')->url($path);
                }
            }catch(\Throwable $__e){ $row['url'] = $path; }
            return $row;
        })->values()->all();

        // normalize rutin_days
        if(!empty($assignment->rutin_days) && is_string($assignment->rutin_days)){
            $decoded = json_decode($assignment->rutin_days, true);
            if(is_array($decoded)) $assignment->rutin_days = $decoded;
        }

        return response()->json(['success' => true, 'assignment' => (array)$assignment, 'photos' => $photos]);
    }
}
