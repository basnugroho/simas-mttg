<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mosque;
use App\Models\Facility;
use App\Models\MosqueFacility;

class MosqueFacilityController extends Controller
{
    public function show(Mosque $mosque)
    {
        // return JSON listing all facilities with current assignment data for this mosque
        $facilities = Facility::with('unit')->get();
        $assigned = MosqueFacility::where('mosque_id', $mosque->id)->get()->keyBy('facility_id');

        $data = $facilities->map(function ($f) use ($assigned) {
            $a = $assigned->get($f->id);
            return [
                'id' => $f->id,
                'name' => $f->name,
                'slug' => $f->slug,
                'is_required' => (bool) $f->is_required,
                'unit' => $f->unit ? ['id' => $f->unit->id, 'name' => $f->unit->name] : null,
                'assignment' => $a ? [
                    'id' => $a->id,
                    'is_available' => (bool) $a->is_available,
                    'quantity' => $a->quantity,
                    'unit_id' => $a->unit_id,
                    'note' => $a->note,
                ] : null,
            ];
        });

        return response()->json(['success' => true, 'facilities' => $data]);
    }

    public function update(Request $request, Mosque $mosque)
    {
        // Expect JSON payload: { facilities: { <id>: { is_available: 0|1, quantity, unit_id, note } } }
        $payload = $request->input('facilities', []);
        if (!is_array($payload)) {
            return response()->json(['success' => false, 'message' => 'Invalid payload'], 422);
        }

        foreach ($payload as $fid => $data) {
            $fid = (int) $fid;
            $isAvailable = !empty($data['is_available']) ? 1 : 0;
            $quantity = array_key_exists('quantity', $data) ? $data['quantity'] : null;
            $unitId = array_key_exists('unit_id', $data) ? $data['unit_id'] : null;
            $note = array_key_exists('note', $data) ? $data['note'] : null;

            MosqueFacility::updateOrCreate([
                'mosque_id' => $mosque->id,
                'facility_id' => $fid,
            ], [
                'is_available' => $isAvailable,
                'quantity' => $quantity,
                'unit_id' => $unitId,
                'note' => $note,
            ]);
        }

        // Recalculate completion percentage: count required facilities and how many are available
        try {
            $totalRequired = \App\Models\Facility::where('is_required', true)->count();
            if ($totalRequired > 0) {
                $availableRequired = \App\Models\MosqueFacility::where('mosque_id', $mosque->id)
                    ->where('is_available', 1)
                    ->whereHas('facility', function ($q) { $q->where('is_required', true); })
                    ->count();

                $percent = (int) round(($availableRequired / $totalRequired) * 100);
                $mosque->update(['completion_percentage' => $percent]);
            }
        } catch (\Throwable $e) {
            // non-fatal: log and continue
            try { \Illuminate\Support\Facades\Log::warning('Failed recalculating completion for mosque '.$mosque->id, ['err' => (string)$e]); } catch (\Throwable $_) {}
        }

        return response()->json(['success' => true, 'completion_percentage' => $mosque->completion_percentage]);
    }
}
