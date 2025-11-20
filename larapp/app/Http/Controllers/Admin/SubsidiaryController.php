<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subsidiary;
use Illuminate\Http\Request;

class SubsidiaryController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $query = Subsidiary::query();
        if ($q) $query->where('name', 'like', "%{$q}%");
        $items = $query->orderBy('name')->paginate(20)->appends(['q'=>$q]);
        return view('admin.master.subsidiaries.index', compact('items', 'q'));
    }

    public function create()
    {
        return view('admin.master.subsidiaries.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
        ]);
        Subsidiary::create($data + ['created_by' => auth()->id()]);
        return redirect()->route('admin.subsidiaries.index')->with('success', 'Subsidiary created');
    }

    public function edit(Subsidiary $subsidiary)
    {
        return view('admin.master.subsidiaries.edit', compact('subsidiary'));
    }

    public function update(Request $request, Subsidiary $subsidiary)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
        ]);
        $subsidiary->update($data);
        return redirect()->route('admin.subsidiaries.index')->with('success', 'Subsidiary updated');
    }

    public function destroy(Subsidiary $subsidiary)
    {
        $subsidiary->delete();
        return redirect()->route('admin.subsidiaries.index')->with('success', 'Subsidiary deleted');
    }
}
