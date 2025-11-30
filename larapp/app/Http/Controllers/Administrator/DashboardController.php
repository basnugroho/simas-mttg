<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Mosque;
use App\Models\Regions;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil region dengan level REGIONAL atau type_key AREA (fallback legacy type PROVINCE)
        $regionsQuery = Regions::query()
            ->where(function ($q) {
                $q->where('level', 'REGIONAL')
                    ->orWhere('type_key', 'AREA')
                    ->orWhere('type', 'PROVINCE');
            })
            ->orderBy('name');

        $regions = $regionsQuery->get();

        // Ambil agregasi count masjid & musholla per province_id sekali query
        $rawCounts = Mosque::select(
            'province_id',
            DB::raw("SUM(CASE WHEN type='MASJID' THEN 1 ELSE 0 END) AS masjid_count"),
            DB::raw("SUM(CASE WHEN type='MUSHOLLA' THEN 1 ELSE 0 END) AS musholla_count")
        )
            ->whereNotNull('province_id')
            ->groupBy('province_id')
            ->get()
            ->keyBy('province_id');

        $regionCards = $regions->map(function ($r) use ($rawCounts) {
            $counts = $rawCounts->get($r->id);
            $masjid = $counts?->masjid_count ?? 0;
            $musholla = $counts?->musholla_count ?? 0;

            return [
                'id' => $r->id,
                'name' => $r->name,
                'masjid' => $masjid,
                'musholla' => $musholla,
                'total' => $masjid + $musholla,
            ];
        });

        $totalMasjid = $regionCards->sum('masjid');
        $totalMusholla = $regionCards->sum('musholla');
        $grandTotal = $totalMasjid + $totalMusholla;

        return view('administrator.dashboard.index', compact('regionCards', 'totalMasjid', 'totalMusholla', 'grandTotal'));
    }
}
