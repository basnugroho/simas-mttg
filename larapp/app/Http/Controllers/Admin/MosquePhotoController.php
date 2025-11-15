<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MosquePhoto;
use Illuminate\Support\Facades\Storage;

class MosquePhotoController extends Controller
{
    public function destroy(MosquePhoto $photo)
    {
        try {
            if ($photo->path) {
                Storage::disk('public')->delete($photo->path);
            }
        } catch (\Throwable $e) {
            // ignore file delete errors
        }
        $photo->delete();
        return back()->with('success', 'Photo deleted');
    }
}
