<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $query = Facility::query();
        if ($q) $query->where('name', 'like', "%{$q}%");
        $items = $query->orderBy('name')->paginate(20);
        return view('admin.master.facilities.index', compact('items', 'q'));
    }

    public function create()
    {
        return view('admin.master.facilities.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'is_required' => 'nullable|boolean',
        ]);
        $data['is_required'] = $request->has('is_required');
        Facility::create($data);
        return redirect()->route('admin.facilities.index')->with('success', 'Facility created');
    }

    public function edit(Facility $facility)
    {
        return view('admin.master.facilities.edit', compact('facility'));
    }

    public function update(Request $request, Facility $facility)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'is_required' => 'nullable|boolean',
        ]);
        $data['is_required'] = $request->has('is_required');
        $facility->update($data);
        return redirect()->route('admin.facilities.index')->with('success', 'Facility updated');
    }

    public function destroy(Facility $facility)
    {
        $facility->delete();
        return redirect()->route('admin.facilities.index')->with('success', 'Facility deleted');
    }
}
