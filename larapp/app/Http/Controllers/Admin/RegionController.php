<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Regions;
use Illuminate\Http\Request;

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
        $regions = $query->orderBy('name')->paginate(20);
        return view('admin.master.regions.index', compact('regions', 'q'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parents = Regions::orderBy('name')->get();
        return view('admin.master.regions.create', compact('parents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:50',
            'code' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:regions,id',
        ]);

        Regions::create($data);
        return redirect()->route('admin.regions.index')->with('success', 'Region created.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Regions $region)
    {
        $parents = Regions::where('id', '!=', $region->id)->orderBy('name')->get();
        return view('admin.master.regions.edit', ['region' => $region, 'parents' => $parents]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Regions $region)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:50',
            'code' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:regions,id',
        ]);

        $region->update($data);
        return redirect()->route('admin.regions.index')->with('success', 'Region updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Regions $region)
    {
        $region->delete();
        return redirect()->route('admin.regions.index')->with('success', 'Region deleted.');
    }
}
