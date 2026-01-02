<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CashPosition;
use App\Models\CashPositionPhoto;
use App\Models\Mosque;
use Illuminate\Support\Facades\Storage;

class CashPositionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        // limit mosques to regions the user can access
        $regionIds = $user ? $user->getEffectiveRegionIds() : [];

        $mosqueQuery = Mosque::orderBy('name');
        if(!empty($regionIds)){
            $mosqueQuery->where(function($q) use ($regionIds){
                $q->whereIn('province_id', $regionIds)
                  ->orWhereIn('regional_id', $regionIds)
                  ->orWhereIn('area_id', $regionIds)
                  ->orWhereIn('city_id', $regionIds)
                  ->orWhereIn('witel_id', $regionIds)
                  ->orWhereIn('sto_id', $regionIds);
            });
        }
        $mosques = $mosqueQuery->get();

        $positionsQuery = CashPosition::with('mosque','photos','creator')
            ->where('is_deleted', false);

        // apply mosque filter if provided
        if($request->filled('mosque_id')){
            $positionsQuery->where('mosque_id', $request->input('mosque_id'));
        }

        // period range filters (by period_start)
        if($request->filled('period_from')){
            $positionsQuery->whereDate('period_start', '>=', $request->input('period_from'));
        }
        if($request->filled('period_to')){
            $positionsQuery->whereDate('period_start', '<=', $request->input('period_to'));
        }

        // sorting
        $allowedSort = ['created_at','period_start','nominal','mosque'];
        $sort = in_array($request->input('sort'), $allowedSort) ? $request->input('sort') : 'created_at';
        $dir = $request->input('dir') === 'asc' ? 'asc' : 'desc';

        if($sort === 'mosque'){
            // join mosques to sort by name
            $positionsQuery = $positionsQuery->leftJoin('mosques', 'cash_positions.mosque_id', '=', 'mosques.id')
                ->select('cash_positions.*')
                ->orderBy('mosques.name', $dir);
        }else{
            $positionsQuery = $positionsQuery->orderBy($sort, $dir);
        }

        $positions = $positionsQuery->paginate(20)->appends($request->query());

        return view('admin.cash_positions.index', [
            'mosques' => $mosques,
            'positions' => $positions,
            'selected_mosque' => $request->input('mosque_id'),
            'filter_period_from' => $request->input('period_from'),
            'filter_period_to' => $request->input('period_to'),
            'sort' => $sort,
            'dir' => $dir,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'period_start' => 'nullable|date',
            'mosque_id' => 'required|exists:mosques,id',
            'nominal' => 'nullable|numeric|min:0',
            'files.*' => 'nullable|file|max:10240',
            'note' => 'nullable|string|max:2000',
        ]);

        $cp = new CashPosition();
        $cp->mosque_id = $data['mosque_id'];
        $cp->period_start = $data['period_start'] ?? null;
        // Auto-calculate period_end as last day of the month
        if (!empty($data['period_start'])) {
            $startDate = \Carbon\Carbon::parse($data['period_start']);
            $cp->period_end = $startDate->endOfMonth()->toDateString();
        } else {
            $cp->period_end = null;
        }
        $cp->nominal = $data['nominal'] ?? null;
        $cp->note = $data['note'] ?? null;
        $cp->created_by = auth()->id();
        $cp->save();

        // handle generic files (not restricted to images)
        if($request->hasFile('files')){
            foreach($request->file('files') as $i => $f){
                if(!$f->isValid()) continue;
                $path = $f->store('cash_positions/'.$cp->id, 'public');
                $photo = new CashPositionPhoto();
                $photo->cash_position_id = $cp->id;
                $photo->path = $path;
                $photo->sort_order = $i;
                $photo->caption = null;
                $photo->save();
            }
        }

        return redirect()->route('admin.cash_positions.index')->with('success','Cash position uploaded');
    }

    public function edit($id)
    {
        $cp = CashPosition::with('photos','mosque')->findOrFail($id);
        if($cp->is_deleted){ abort(404); }
        $mosques = Mosque::orderBy('name')->get();
        return view('admin.cash_positions.edit', ['cp' => $cp, 'mosques' => $mosques]);
    }

    public function update(Request $request, $id)
    {
        $cp = CashPosition::findOrFail($id);
        if($cp->is_deleted){ abort(404); }

        $data = $request->validate([
            'period_start' => 'nullable|date',
            'mosque_id' => 'required|exists:mosques,id',
            'nominal' => 'nullable|numeric|min:0',
            'files.*' => 'nullable|mimes:pdf|max:10240',
            'note' => 'nullable|string|max:2000',
            'edit_note' => 'nullable|string|max:2000',
        ]);

        $cp->mosque_id = $data['mosque_id'];
        $cp->period_start = $data['period_start'] ?? null;
        // Auto-calculate period_end as last day of the month
        if (!empty($data['period_start'])) {
            $startDate = \Carbon\Carbon::parse($data['period_start']);
            $cp->period_end = $startDate->endOfMonth()->toDateString();
        } else {
            $cp->period_end = null;
        }
        $cp->nominal = $data['nominal'] ?? null;
        $cp->note = $data['note'] ?? null;
        $cp->edited_by = auth()->id();
        $cp->edited_at = now();
        $cp->edit_note = $data['edit_note'] ?? null;
        $cp->save();

        if($request->hasFile('files')){
            foreach($request->file('files') as $i => $f){
                if(!$f->isValid()) continue;
                $path = $f->store('cash_positions/'.$cp->id, 'public');
                $photo = new CashPositionPhoto();
                $photo->cash_position_id = $cp->id;
                $photo->path = $path;
                $photo->sort_order = $photo->id ?? $i;
                $photo->caption = null;
                $photo->save();
            }
        }

        return redirect()->route('admin.cash_positions.index')->with('success','Cash position updated');
    }

    public function destroy($id)
    {
        $cp = CashPosition::findOrFail($id);
        $cp->is_deleted = true;
        $cp->save();
        return redirect()->route('admin.cash_positions.index')->with('success','Cash position removed');
    }
}
