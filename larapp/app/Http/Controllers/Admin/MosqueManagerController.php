<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MosqueManager;
use App\Models\Mosque;
use Illuminate\Support\Facades\Storage;

class MosqueManagerController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
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

        $query = MosqueManager::with('mosque','creator')
            ->where('is_deleted', false)
            ->orderBy('created_at','desc');

        if($request->filled('mosque_id')) $query->where('mosque_id', $request->input('mosque_id'));

        $items = $query->paginate(20)->appends($request->query());

        return view('admin.mosque_managers.index', [
            'mosques' => $mosques,
            'items' => $items,
            'selected_mosque' => $request->input('mosque_id'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'mosque_id' => 'required|exists:mosques,id',
            'jumlah_pengurus' => 'nullable|integer|min:0',
            'ketua_pengurus' => 'nullable|string|max:255',
            'file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $m = new MosqueManager();
        $m->mosque_id = $data['mosque_id'];
        $m->jumlah_pengurus = $data['jumlah_pengurus'] ?? null;
        $m->ketua_pengurus = $data['ketua_pengurus'] ?? null;
        $m->created_by = auth()->id();
        $m->save();

        if($request->hasFile('file')){
            $f = $request->file('file');
            if($f->isValid()){
                $path = $f->store('mosque_managers/'.$m->id, 'public');
                $m->file_path = $path; $m->save();
            }
        }

        return redirect()->route('admin.mosque_managers.index')->with('success','Pengurus Masjid disimpan');
    }

    public function edit($id)
    {
        $m = MosqueManager::with('mosque','creator')->findOrFail($id);
        if($m->is_deleted) abort(404);
        $mosques = Mosque::orderBy('name')->get();
        return view('admin.mosque_managers.edit', ['m' => $m, 'mosques' => $mosques]);
    }

    public function update(Request $request, $id)
    {
        $m = MosqueManager::findOrFail($id);
        if($m->is_deleted) abort(404);

        $data = $request->validate([
            'mosque_id' => 'required|exists:mosques,id',
            'jumlah_pengurus' => 'nullable|integer|min:0',
            'ketua_pengurus' => 'nullable|string|max:255',
            'file' => 'nullable|file|mimes:pdf|max:10240',
            'edit_note' => 'nullable|string|max:2000',
        ]);

        $m->mosque_id = $data['mosque_id'];
        $m->jumlah_pengurus = $data['jumlah_pengurus'] ?? null;
        $m->ketua_pengurus = $data['ketua_pengurus'] ?? null;
        $m->edited_by = auth()->id();
        $m->edited_at = now();
        $m->edit_note = $data['edit_note'] ?? null;
        $m->save();

        if($request->hasFile('file')){
            $f = $request->file('file');
            if($f->isValid()){
                $path = $f->store('mosque_managers/'.$m->id, 'public');
                $m->file_path = $path; $m->save();
            }
        }

        return redirect()->route('admin.mosque_managers.index')->with('success','Pengurus Masjid diperbarui');
    }

    public function destroy($id)
    {
        $m = MosqueManager::findOrFail($id);
        $m->is_deleted = true; $m->save();
        return redirect()->route('admin.mosque_managers.index')->with('success','Item dihapus');
    }
}
