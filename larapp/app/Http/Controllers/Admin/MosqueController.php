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
        $query = Mosque::query();
        if ($q) $query->where('name', 'like', "%{$q}%");
        $mosques = $query->orderBy('name')->paginate(20);
        return view('admin.master.mosques.index', compact('mosques', 'q'));
    }

    public function create()
    {
        // provide lists for hierarchical region selection
        $regionals = Regions::where('level', 'REGIONAL')->orderBy('name')->get();
        $witels = Regions::where('level', 'WITEL')->orderBy('name')->get();
        $stos = Regions::where('level', 'STO')->orderBy('name')->get();
        $mosque = new Mosque();
        return view('admin.master.mosques.create', compact('mosque', 'regionals', 'witels', 'stos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'type' => 'nullable|string|in:MASJID,MUSHOLLA',
            'address' => 'nullable|string',
            'tahun_didirikan' => 'nullable|integer|min:1800|max:2100',
            'jml_bkm' => 'nullable|integer|min:0',
            'luas_tanah' => 'nullable|numeric|min:0',
            'daya_tampung' => 'nullable|integer|min:0',
            'regional_id' => 'nullable|exists:regions,id',
            'witel_id' => 'nullable|exists:regions,id',
            'sto_id' => 'nullable|exists:regions,id',
        ]);
        $mosque = Mosque::create($data);

        // handle photo uploads (multiple)
        $request->validate([
            'photos.*' => 'image|max:5120', // max 5MB per file
            'photo_captions.*' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('photos')) {
            $files = $request->file('photos');
            $captions = $request->input('photo_captions', []);
            foreach ($files as $i => $f) {
                if (!$f->isValid()) continue;
                $path = $f->store("mosques/{$mosque->id}", 'public');
                // generate thumbnail
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
        return view('admin.master.mosques.edit', compact('mosque', 'regionals', 'witels', 'stos'));
    }

    public function update(Request $request, Mosque $mosque)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'type' => 'nullable|string|in:MASJID,MUSHOLLA',
            'address' => 'nullable|string',
            'tahun_didirikan' => 'nullable|integer|min:1800|max:2100',
            'jml_bkm' => 'nullable|integer|min:0',
            'luas_tanah' => 'nullable|numeric|min:0',
            'daya_tampung' => 'nullable|integer|min:0',
            'regional_id' => 'nullable|exists:regions,id',
            'witel_id' => 'nullable|exists:regions,id',
            'sto_id' => 'nullable|exists:regions,id',
        ]);
        $mosque->update($data);

        // handle additional photo uploads
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
        return redirect()->route('admin.mosques.index')->with('success', 'Mosque updated');
    }

    /**
     * Generate a thumbnail for a stored image path on the public disk.
     * Thumbnail is saved next to original with prefix thumb_ (same directory).
     */
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
            // preserve transparency for PNG/GIF
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
            imagecopyresampled($thumb, $src, 0,0,0,0, $tw, $th, $w, $h);
            $thumbPath = dirname($publicPath) . '/thumb_' . basename($publicPath);
            $fullThumb = $disk->path($thumbPath);
            // ensure directory exists
            @mkdir(dirname($fullThumb), 0755, true);
            // write JPEG or PNG depending on original mime
            $ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));
            if (in_array($ext, ['png'])) {
                imagepng($thumb, $fullThumb, 6);
            } else {
                // default to JPEG
                imagejpeg($thumb, $fullThumb, 85);
            }
            imagedestroy($src); imagedestroy($thumb);
        } catch (\Throwable $e) {
            // non-fatal
        }
    }

    public function destroy(Mosque $mosque)
    {
        $mosque->delete();
        return redirect()->route('admin.mosques.index')->with('success', 'Mosque deleted');
    }
}
