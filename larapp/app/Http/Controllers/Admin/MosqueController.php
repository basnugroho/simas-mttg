<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mosque;
use App\Models\Regions;
use App\Models\MosquePhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MosqueController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $query = Mosque::with(['regional','area','witel','sto','province','city','photos']);
        if ($q) $query->where('name', 'like', "%{$q}%");
        $mosques = $query->orderBy('name')->paginate(20);
        return view('admin.master.mosques.index', compact('mosques', 'q'));
    }

    public function create()
    {
        $regionals = Regions::where('level', 'REGIONAL')->orderBy('name')->get();
        $witels = Regions::where('level', 'WITEL')->orderBy('name')->get();
        $stos = Regions::where('level', 'STO')->orderBy('name')->get();
        $regions = collect();
        $mosque = new Mosque();

        $me = auth()->user();
        $myRole = $me ? $me->role : null;
        $myAssignments = [];
        $lockedValues = [];
        $lockedFields = [];
        if ($me) {
            $grouped = [];
            foreach ($me->regionsRoles()->get() as $ar) { $grouped[$ar->role_key][] = (int)$ar->region_id; }
            $myAssignments = $grouped;
            $priority = ['admin_sto','admin_witel','admin_area','admin_regional'];
            foreach ($priority as $rk) {
                if (!empty($grouped[$rk]) && count($grouped[$rk]) === 1) {
                    $rid = (int)$grouped[$rk][0];
                    try {
                        $r = Regions::find($rid);
                        if ($r) {
                            $level = strtoupper($r->level ?? '');
                            if ($level === 'STO') {
                                $lockedValues['sto_id'] = $r->id; $lockedFields[] = 'sto_id';
                                if ($r->parent_id) { $p = Regions::find($r->parent_id); if ($p) { $lockedValues['witel_id'] = $p->id; $lockedFields[] = 'witel_id'; } if ($p && $p->parent_id) { $pp = Regions::find($p->parent_id); if ($pp) { $lockedValues['area_id'] = $pp->id; $lockedFields[] = 'area_id'; } if ($pp && $pp->parent_id) { $ppp = Regions::find($pp->parent_id); if ($ppp) { $lockedValues['regional_id'] = $ppp->id; $lockedFields[] = 'regional_id'; } } } }
                            } elseif ($level === 'WITEL') {
                                $lockedValues['witel_id'] = $r->id; $lockedFields[] = 'witel_id';
                                if ($r->parent_id) { $p = Regions::find($r->parent_id); if ($p) { $lockedValues['area_id'] = $p->id; $lockedFields[] = 'area_id'; } if ($p && $p->parent_id) { $pp = Regions::find($p->parent_id); if ($pp) { $lockedValues['regional_id'] = $pp->id; $lockedFields[] = 'regional_id'; } } }
                            } elseif ($level === 'AREA') {
                                $lockedValues['area_id'] = $r->id; $lockedFields[] = 'area_id';
                                if ($r->parent_id) { $p = Regions::find($r->parent_id); if ($p) { $lockedValues['regional_id'] = $p->id; $lockedFields[] = 'regional_id'; } }
                            } elseif ($level === 'REGIONAL') {
                                $lockedValues['regional_id'] = $r->id; $lockedFields[] = 'regional_id';
                            }
                        }
                    } catch (\Throwable $e) {}
                    break;
                }
            }
        }

        $lockedLabels = [];
        if (!empty($lockedValues)) {
            foreach ($lockedValues as $field => $id) { try { $r = Regions::find($id); if ($r) $lockedLabels[$field] = $r->name; } catch (\Throwable $e) { } }
        }

        return view('admin.master.mosques.create', compact('mosque', 'regionals', 'witels', 'stos', 'regions', 'myRole', 'myAssignments', 'lockedValues', 'lockedFields', 'lockedLabels'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'type' => 'nullable|string|in:MASJID,MUSHALLA,MUSHOLLA',
            'province' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'tahun_didirikan' => 'nullable|integer|min:1800|max:2100',
            'jml_bkm' => 'nullable|integer|min:0',
            'luas_tanah' => 'nullable|numeric|min:0',
            'daya_tampung' => 'nullable|integer|min:0',
            'regional_id' => 'nullable|exists:regions,id',
            'area_id' => 'nullable|exists:regions,id',
            'witel_id' => 'nullable|exists:regions,id',
            'sto_id' => 'nullable|exists:regions,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $candidate = new Mosque($data);
        $this->authorize('create', $candidate);
        // normalize type variants (accept MUSHOLLA but store canonical MUSHALLA)
        if (!empty($data['type']) && strtolower($data['type']) === 'musholla') {
            $data['type'] = 'MUSHALLA';
        }
        $mosque = Mosque::create($data);

        $request->validate([
            'photos.*' => 'image|max:5120',
            'photo_captions.*' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('photos')) {
            $files = $request->file('photos');
            $captions = $request->input('photo_captions', []);
            foreach ($files as $i => $f) {
                if (!$f->isValid()) continue;
                $path = $f->store("mosques/{$mosque->id}", 'public');
                $this->generateThumbnail($path);
                MosquePhoto::create([
                    'mosque_id' => $mosque->id,
                    'path' => $path,
                    'caption' => $captions[$i] ?? null,
                ]);
            }
        }
        return redirect()->route('admin.mosques.index')->with('success', 'Mosque created');
    }

    public function edit(Mosque $mosque)
    {
        $regionals = Regions::where('level', 'REGIONAL')->orderBy('name')->get();
        $witels = Regions::where('level', 'WITEL')->orderBy('name')->get();
        $stos = Regions::where('level', 'STO')->orderBy('name')->get();
        $regions = collect();
        if($mosque->regional_id){
            $desc = Regions::collectDescendantIds((int)$mosque->regional_id);
            $regions = Regions::whereIn('id', $desc)->orderBy('name')->get();
        }

        $me = auth()->user();
        $myRole = $me ? $me->role : null;
        $myAssignments = [];
        $lockedValues = [];
        $lockedFields = [];
        if ($me) {
            $grouped = [];
            foreach ($me->regionsRoles()->get() as $ar) { $grouped[$ar->role_key][] = (int)$ar->region_id; }
            $myAssignments = $grouped;
            $priority = ['admin_sto','admin_witel','admin_area','admin_regional'];
            foreach ($priority as $rk) {
                if (!empty($grouped[$rk]) && count($grouped[$rk]) === 1) {
                    $rid = (int)$grouped[$rk][0];
                    try {
                        $r = Regions::find($rid);
                        if ($r) {
                            $level = strtoupper($r->level ?? '');
                            if ($level === 'STO') {
                                $lockedValues['sto_id'] = $r->id; $lockedFields[] = 'sto_id';
                                if ($r->parent_id) { $p = Regions::find($r->parent_id); if ($p) { $lockedValues['witel_id'] = $p->id; $lockedFields[] = 'witel_id'; } if ($p && $p->parent_id) { $pp = Regions::find($p->parent_id); if ($pp) { $lockedValues['area_id'] = $pp->id; $lockedFields[] = 'area_id'; } if ($pp && $pp->parent_id) { $ppp = Regions::find($pp->parent_id); if ($ppp) { $lockedValues['regional_id'] = $ppp->id; $lockedFields[] = 'regional_id'; } } } }
                            } elseif ($level === 'WITEL') {
                                $lockedValues['witel_id'] = $r->id; $lockedFields[] = 'witel_id';
                                if ($r->parent_id) { $p = Regions::find($r->parent_id); if ($p) { $lockedValues['area_id'] = $p->id; $lockedFields[] = 'area_id'; } if ($p && $p->parent_id) { $pp = Regions::find($p->parent_id); if ($pp) { $lockedValues['regional_id'] = $pp->id; $lockedFields[] = 'regional_id'; } } }
                            } elseif ($level === 'AREA') {
                                $lockedValues['area_id'] = $r->id; $lockedFields[] = 'area_id';
                                if ($r->parent_id) { $p = Regions::find($r->parent_id); if ($p) { $lockedValues['regional_id'] = $p->id; $lockedFields[] = 'regional_id'; } }
                            } elseif ($level === 'REGIONAL') {
                                $lockedValues['regional_id'] = $r->id; $lockedFields[] = 'regional_id';
                            }
                        }
                    } catch (\Throwable $e) {}
                    break;
                }
            }
        }

        $lockedLabels = [];
        if (!empty($lockedValues)) { foreach ($lockedValues as $field => $id) { try { $r = Regions::find($id); if ($r) $lockedLabels[$field] = $r->name; } catch (\Throwable $e) { } } }

        return view('admin.master.mosques.edit', compact('mosque', 'regionals', 'witels', 'stos', 'regions', 'myRole', 'myAssignments', 'lockedValues', 'lockedFields', 'lockedLabels'));
    }

    protected function generateThumbnail(string $publicPath)
    {
        try {
            $disk = Storage::disk('public');
            $full = $disk->path($publicPath);
            if (!file_exists($full)) return;
            $imageData = file_get_contents($full);
            if ($imageData === false) return;
            $src = @imagecreatefromstring($imageData);
            if (!$src) return;
            $w = imagesx($src);
            $h = imagesy($src);
            $maxW = 400; $maxH = 300;
            $ratio = min($maxW / $w, $maxH / $h, 1);
            $tw = (int)($w * $ratio);
            $th = (int)($h * $ratio);
            $thumb = imagecreatetruecolor($tw, $th);
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
            imagecopyresampled($thumb, $src, 0,0,0,0, $tw, $th, $w, $h);
            $thumbPath = dirname($publicPath) . '/thumb_' . basename($publicPath);
            $fullThumb = $disk->path($thumbPath);
            @mkdir(dirname($fullThumb), 0755, true);
            $ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));
            if (in_array($ext, ['png'])) { imagepng($thumb, $fullThumb, 6); }
            else { imagejpeg($thumb, $fullThumb, 85); }
            imagedestroy($src); imagedestroy($thumb);
        } catch (\Throwable $e) {
            // non-fatal
        }
    }

    public function update(Request $request, Mosque $mosque)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'type' => 'nullable|string|in:MASJID,MUSHALLA,MUSHOLLA',
            'province' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'tahun_didirikan' => 'nullable|integer|min:1800|max:2100',
            'jml_bkm' => 'nullable|integer|min:0',
            'luas_tanah' => 'nullable|numeric|min:0',
            'daya_tampung' => 'nullable|integer|min:0',
            'regional_id' => 'nullable|exists:regions,id',
            'area_id' => 'nullable|exists:regions,id',
            'witel_id' => 'nullable|exists:regions,id',
            'sto_id' => 'nullable|exists:regions,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        try {
            // Debug/logging: record received files and request keys to help diagnose upload issues
            try { \Illuminate\Support\Facades\Log::info('mosque.update debug', ['files' => array_keys($request->allFiles()), 'count' => count($request->allFiles()), 'hasPhotosFile' => $request->hasFile('photos'), 'request_keys' => array_keys($request->all())]); } catch (\Throwable $__e) { }
            $this->authorize('update', $mosque);
            // normalize type variants
            if (!empty($data['type']) && strtolower($data['type']) === 'musholla') { $data['type'] = 'MUSHALLA'; }
            $mosque->update($data);

            // Process deletions of existing photos (if any)
            $deleteIdsRaw = $request->input('delete_photos', []);
            $deleteIds = is_array($deleteIdsRaw) ? $deleteIdsRaw : ($deleteIdsRaw ? [$deleteIdsRaw] : []);
            foreach ($deleteIds as $pid) {
                if (empty($pid)) continue;
                try {
                    $p = MosquePhoto::where('id', $pid)->where('mosque_id', $mosque->id)->first();
                    if (!$p) continue;
                    // delete files from storage (original + thumb)
                    try { if ($p->path) Storage::disk('public')->delete($p->path); } catch (\Throwable $_) {}
                    try { $thumb = $p->path ? (dirname($p->path).'/thumb_'.basename($p->path)) : null; if ($thumb) Storage::disk('public')->delete($thumb); } catch (\Throwable $_) {}
                    $p->delete();
                } catch (\Throwable $_) { /* ignore individual failures */ }
            }

            // Update captions for existing photos
            $existingCaptions = $request->input('existing_captions', []);
            if (is_array($existingCaptions) && count($existingCaptions)) {
                foreach ($existingCaptions as $pid => $cap) {
                    if (empty($pid)) continue;
                    try {
                        $p = MosquePhoto::where('id', $pid)->where('mosque_id', $mosque->id)->first();
                        if ($p) { $p->caption = $cap; $p->save(); }
                    } catch (\Throwable $_) { }
                }
            }

            // validate and persist newly uploaded photos (if any)
            $request->validate([
                'photos.*' => 'image|max:5120',
                'photo_captions.*' => 'nullable|string|max:255',
            ]);
            $uploadErrors = [];
            if ($request->hasFile('photos')) {
                $files = $request->file('photos');
                $captions = $request->input('photo_captions', []);
                foreach ($files as $i => $f) {
                    try {
                        if (!$f->isValid()) continue;
                        $path = $f->store("mosques/{$mosque->id}", 'public');
                        $this->generateThumbnail($path);
                        MosquePhoto::create([
                            'mosque_id' => $mosque->id,
                            'path' => $path,
                            'caption' => $captions[$i] ?? null,
                        ]);
                    } catch (\Throwable $e) {
                        $uploadErrors[] = ($f->getClientOriginalName() ?? 'file') . ': ' . $e->getMessage();
                    }
                }
            }

            $msg = 'Mosque updated';
            if (!empty($uploadErrors)) { $msg .= ' (some photos failed to upload)'; }
            return redirect()->route('admin.mosques.index')->with('success', $msg);
        } catch (\Throwable $e) {
            error_log('Mosque update failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Update failed. Please try again or contact admin.');
        }
    }

    public function destroy(Mosque $mosque)
    {
        $this->authorize('delete', $mosque);
        $mosque->delete();
        return redirect()->route('admin.mosques.index')->with('success', 'Mosque deleted');
    }
}
