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

    /**
     * Overview grouped by type for frontend facility card
     */
    public function overview()
    {
        // Return a lightweight representation grouped into masjid and musholla for the homepage widget
        $provinceId = request()->query('province_id');
        $cityId = request()->query('city_id');
        $completeness = request()->query('completeness');

        $provinces = \App\Models\Regions::where('type', 'PROVINCE')->orderBy('name')->get();

        $masjidQuery = \App\Models\Mosque::with(['province','city'])
            ->where('type', 'MASJID');
        if($provinceId){ $masjidQuery->where('province_id', $provinceId); }
        if($cityId){ $masjidQuery->where('city_id', $cityId); }
        if($completeness){ $masjidQuery->where('completion_percentage', '>=', (int)$completeness); }
        $masjid = $masjidQuery->orderBy('name')->take(12)->get()->map(function($m){ return [
                'id' => $m->id,
                'name' => $m->name,
                'loc' => trim(($m->province?->name ?? '') . ' / ' . ($m->city?->name ?? '')),
                'img' => $m->image_url ?? ('/images/mosque-'.(rand(1,5)).'.png'),
                'pct' => (int) ($m->completion_percentage ?? 0),
            ]; });

        $mushollaQuery = \App\Models\Mosque::with(['province','city'])->where('type', 'MUSHOLLA');
        if($provinceId){ $mushollaQuery->where('province_id', $provinceId); }
        if($cityId){ $mushollaQuery->where('city_id', $cityId); }
        if($completeness){ $mushollaQuery->where('completion_percentage', '>=', (int)$completeness); }
        $musholla = $mushollaQuery->orderBy('name')->take(12)->get()->map(function($m){ return [
                'id' => $m->id,
                'name' => $m->name,
                'loc' => trim(($m->province?->name ?? '') . ' / ' . ($m->city?->name ?? '')),
                'img' => $m->image_url ?? ('/images/mosque-'.(rand(1,5)).'.png'),
                'pct' => (int) ($m->completion_percentage ?? 0),
            ]; });
        
        return $this->success([
            'masjid' => $masjid,
            'musholla' => $musholla,
            'provinces' => $provinces,
        ], 'Overview fasilitas');
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
