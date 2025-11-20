<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $query = Activity::query();
        if ($q) $query->where('activity_name', 'like', "%{$q}%");
        $items = $query->orderBy('activity_name')->paginate(20)->appends(['q'=>$q]);
        return view('admin.master.activities.index', compact('items', 'q'));
    }

    public function create()
    {
        return view('admin.master.activities.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'activity_name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'category' => 'required|in:mahdhah,ghairu_mahdhah',
        ]);
        Activity::create($data + ['created_by' => auth()->id()]);
        return redirect()->route('admin.activities.index')->with('success', 'Activity created');
    }

    public function edit(Activity $activity)
    {
        return view('admin.master.activities.edit', compact('activity'));
    }

    public function update(Request $request, Activity $activity)
    {
        $data = $request->validate([
            'activity_name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'category' => 'required|in:mahdhah,ghairu_mahdhah',
        ]);
        $activity->update($data);
        return redirect()->route('admin.activities.index')->with('success', 'Activity updated');
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();
        return redirect()->route('admin.activities.index')->with('success', 'Activity deleted');
    }
}
