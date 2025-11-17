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
        $sort = $request->query('sort', 'name');
        $dir = strtolower($request->query('dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        $allowed = ['name', 'slug', 'is_required', 'unit'];
        if (!in_array($sort, $allowed)) $sort = 'name';

        // base query: join unit table when sorting by unit
        $query = Facility::query();
        if ($sort === 'unit') {
            $query = $query->leftJoin('facility_units', 'facilities.unit_id', '=', 'facility_units.id')
                ->select('facilities.*');
            $orderBy = 'facility_units.name';
        } else {
            $orderBy = 'facilities.' . $sort;
        }

        if ($q) $query->where('facilities.name', 'like', "%{$q}%");

        $items = $query->orderBy($orderBy, $dir)->with('unit')->paginate(20)->appends(['q'=>$q, 'sort'=>$sort, 'dir'=>$dir]);
        return view('admin.master.facilities.index', compact('items', 'q', 'sort', 'dir'));
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
            'unit_id' => 'nullable|exists:facility_units,id',
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
            'unit_id' => 'nullable|exists:facility_units,id',
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
