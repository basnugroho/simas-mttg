<?php

namespace App\Http\Controllers\Api;

use App\Http\Traits\ApiResponse;
use App\Models\Facility;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class FacilityController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $facilities = Facility::orderBy('name')->get();
        return $this->success($facilities, 'Daftar fasilitas');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'slug'        => 'required|string|max:100|unique:facilities,slug',
            'is_required' => 'boolean',
        ]);

        $facility = Facility::create($data);

        return $this->success($facility, 'Fasilitas berhasil dibuat', 201);
    }

    public function update(Request $request, $id)
    {
        $facility = Facility::find($id);
        if (! $facility) {
            return $this->error('Data tidak ditemukan', 404);
        }

        $data = $request->validate([
            'name'        => 'sometimes|required|string|max:100',
            'slug'        => 'sometimes|required|string|max:100|unique:facilities,slug,' . $facility->id,
            'is_required' => 'boolean',
        ]);

        $facility->update($data);

        return $this->success($facility, 'Fasilitas berhasil diupdate');
    }

    public function destroy($id)
    {
        $facility = Facility::find($id);
        if (! $facility) {
            return $this->error('Data tidak ditemukan', 404);
        }

        $facility->delete();

        return response()->noContent();
    }
}
