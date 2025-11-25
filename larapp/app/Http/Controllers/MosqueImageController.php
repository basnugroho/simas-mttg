<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class MosqueImageController extends Controller
{
    /**
     * Serve an image stored under storage/app/mosque/*
     */
    public function show(Request $request, $path)
    {
        // Normalize path
        $path = ltrim($path, '/');
        // Only allow files under mosque/ or directly filenames in root
        $candidate = $path;
        // Try candidates: as-is and prefixed with mosque/
        $candidates = [$candidate, 'mosque/' . $candidate];

        foreach ($candidates as $p) {
            if (Storage::disk('local')->exists($p)) {
                $stream = Storage::disk('local')->getDriver()->readStream($p);
                $mime = Storage::disk('local')->mimeType($p) ?? 'application/octet-stream';
                return Response::stream(function () use ($stream) {
                    fpassthru($stream);
                }, 200, [
                    'Content-Type' => $mime,
                    'Content-Disposition' => 'inline; filename="' . basename($p) . '"'
                ]);
            }
        }

        // Not found -> return 404 image or fallback
        abort(404);
    }
}
