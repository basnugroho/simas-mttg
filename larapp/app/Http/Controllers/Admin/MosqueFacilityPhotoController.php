<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mosque;
use App\Models\Facility;
use App\Models\MosqueFacility;
use App\Models\MosqueFacilityPhoto;
use Illuminate\Support\Facades\Storage;

class MosqueFacilityPhotoController extends Controller
{
    protected function findMosqueFacility($mosqueId, $facilityId)
    {
        return MosqueFacility::where('mosque_id', $mosqueId)->where('facility_id', $facilityId)->first();
    }

    public function index(Mosque $mosque, $facility)
    {
        $mf = $this->findMosqueFacility($mosque->id, $facility);
        if (!$mf) return response()->json(['success' => true, 'photos' => []]);
        $photos = $mf->photos->map(function ($p) { return ['id' => $p->id, 'path' => Storage::url($p->path), 'caption' => $p->caption]; });
        return response()->json(['success' => true, 'photos' => $photos]);
    }

    public function store(Request $request, Mosque $mosque, $facility)
    {
        $mf = $this->findMosqueFacility($mosque->id, $facility);
        if (!$mf) return response()->json(['success' => false, 'message' => 'Assignment not found'], 404);

        if (!$request->hasFile('photos')) return response()->json(['success' => false, 'message' => 'No files uploaded'], 422);

        $saved = [];
        $skipped = [];
        foreach ($request->file('photos') as $idx => $file) {
            if (!$file->isValid()) { $skipped[] = ['index'=>$idx,'reason'=>'invalid']; continue; }
            $ext = strtolower($file->getClientOriginalExtension() ?: '');
            $mime = strtolower($file->getMimeType() ?: '');
            // accept only jpeg/jpg
            if (!in_array($ext, ['jpg','jpeg']) && $mime !== 'image/jpeg'){
                $skipped[] = ['index'=>$idx,'name'=>$file->getClientOriginalName(),'reason'=>'not-jpg'];
                continue;
            }
            // limit size 6MB as a safeguard
            if ($file->getSize() > 6 * 1024 * 1024){
                $skipped[] = ['index'=>$idx,'name'=>$file->getClientOriginalName(),'reason'=>'too_large'];
                continue;
            }
            $path = $file->store('mosque_facility_photos', 'public');
            $caption = $request->input('captions.' . $idx, null);
            $photo = MosqueFacilityPhoto::create([
                'mosque_facility_id' => $mf->id,
                'path' => $path,
                'caption' => $caption,
            ]);
            $saved[] = ['id' => $photo->id, 'path' => Storage::url($path), 'caption' => $caption];
        }

        return response()->json(['success' => true, 'photos' => $saved, 'skipped' => $skipped]);
    }

    public function update(Request $request, MosqueFacilityPhoto $photo)
    {
        $caption = $request->input('caption', null);
        $photo->caption = $caption;
        $photo->save();
        return response()->json(['success' => true, 'photo' => ['id' => $photo->id, 'caption' => $photo->caption, 'path' => Storage::url($photo->path)]]);
    }

    public function destroy(MosqueFacilityPhoto $photo)
    {
        try {
            if ($photo->path) Storage::disk('public')->delete($photo->path);
        } catch (\Throwable $e) {
            // ignore
        }
        $photo->delete();
        return response()->json(['success' => true]);
    }
}
