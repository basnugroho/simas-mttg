<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mosque;
use App\Models\Regions;
use Illuminate\Http\Request;

class MosqueController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $query = Mosque::query();
        if ($q) $query->where('name', 'like', "%{$q}%");
        $mosques = $query->orderBy('name')->paginate(20);
        return view('admin.master.mosques.index', compact('mosques', 'q'));
    }

    public function create()
    {
        $provinces = Regions::where('type', 'PROVINCE')->orderBy('name')->get();
        return view('admin.master.mosques.create', compact('provinces'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'type' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'province_id' => 'nullable|exists:regions,id',
        ]);
        Mosque::create($data);
        return redirect()->route('admin.mosques.index')->with('success', 'Mosque created');
    }

    public function edit(Mosque $mosque)
    {
        $provinces = Regions::where('type', 'PROVINCE')->orderBy('name')->get();
        return view('admin.master.mosques.edit', compact('mosque', 'provinces'));
    }

    public function update(Request $request, Mosque $mosque)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'type' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'province_id' => 'nullable|exists:regions,id',
        ]);
        $mosque->update($data);
        return redirect()->route('admin.mosques.index')->with('success', 'Mosque updated');
    }

    public function destroy(Mosque $mosque)
    {
        $mosque->delete();
        return redirect()->route('admin.mosques.index')->with('success', 'Mosque deleted');
    }
}
