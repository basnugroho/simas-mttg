<?php
    
namespace App\Http\Controllers\Home\Beranda;

use App\Http\Controllers\Controller;
use App\Models\Regions;
use App\Models\Mosque;
use App\Models\Article;
use App\Models\Facility;

class BerandaController extends Controller
{
    public function index()
    {
        // Fetch a few regions as example (top-level provinces) with counts
        $regions = Regions::query()
            ->where('type', 'PROVINCE')
            ->withCount([
                'mosques as masjid_count' => function ($q) { $q->where('type', 'MASJID')->where('is_active', true); },
                'mosques as musholla_count' => function ($q) { $q->where('type', 'MUSHOLLA')->where('is_active', true); },
                // BKM is not a mosque type; we'll compute from jml_bkm per region below
                // facility averages per region will be computed below
            ])
            ->orderBy('name')
            ->take(3)
            ->get()
            ->map(function ($region) {
                // Compute BKM total per region by summing jml_bkm on mosques linked via area_id
                $region->bkm_count = (int) Mosque::where('area_id', $region->id)->where('is_active', true)->sum('jml_bkm');

                // Compute average number of available facilities per mosque for MASJID and MUSHOLLA in this area
                $masjidIds = Mosque::where('area_id', $region->id)->where('is_active', true)->where('type', 'MASJID')->pluck('id');
                $mushollaIds = Mosque::where('area_id', $region->id)->where('is_active', true)->where('type', 'MUSHOLLA')->pluck('id');

                $avgMasjidFacilities = 0;
                if ($masjidIds->count() > 0) {
                    $totalMasjidFacilities = \DB::table('mosque_facility')
                        ->whereIn('mosque_id', $masjidIds)
                        ->where('is_available', 1)
                        ->count();
                    $avgMasjidFacilities = round($totalMasjidFacilities / $masjidIds->count(), 2);
                }

                $avgMushollaFacilities = 0;
                if ($mushollaIds->count() > 0) {
                    $totalMushollaFacilities = \DB::table('mosque_facility')
                        ->whereIn('mosque_id', $mushollaIds)
                        ->where('is_available', 1)
                        ->count();
                    $avgMushollaFacilities = round($totalMushollaFacilities / $mushollaIds->count(), 2);
                }

                // Convert averages to percentage of total facility types
                $facilityTypes = (int) Facility::count();
                $percentMasjidFacilities = $facilityTypes > 0 ? round(($avgMasjidFacilities / $facilityTypes) * 100, 2) : 0;
                $percentMushollaFacilities = $facilityTypes > 0 ? round(($avgMushollaFacilities / $facilityTypes) * 100, 2) : 0;

                $region->avg_facilities_masjid = $avgMasjidFacilities;
                $region->avg_facilities_musholla = $avgMushollaFacilities;
                $region->avg_facilities_masjid_percent = $percentMasjidFacilities;
                $region->avg_facilities_musholla_percent = $percentMushollaFacilities;
                return $region;
            });

        // Regional list for filter (level = REGIONAL)
        $regionals = Regions::query()
            ->where('level', 'REGIONAL')
            ->orderBy('name')
            ->get();

        // Build a summary from Mosques
        $masjidTotal = Mosque::where('type', 'MASJID')->where('is_active', true)->count();
        $mushollaTotal = Mosque::where('type', 'MUSHOLLA')->where('is_active', true)->count();
        $bkmTotal = (int) Mosque::where('is_active', true)->sum('jml_bkm');

        // Overall facility averages (active only)
        $masjidIdsAll = Mosque::where('is_active', true)->where('type', 'MASJID')->pluck('id');
        $mushollaIdsAll = Mosque::where('is_active', true)->where('type', 'MUSHOLLA')->pluck('id');
        $avgFacilitiesMasjidTotal = 0;
        if ($masjidIdsAll->count() > 0) {
            $totalMasjidFacilitiesAll = \DB::table('mosque_facility')
                ->whereIn('mosque_id', $masjidIdsAll)
                ->where('is_available', 1)
                ->count();
            $avgFacilitiesMasjidTotal = round($totalMasjidFacilitiesAll / $masjidIdsAll->count(), 2);
        }
        $avgFacilitiesMushollaTotal = 0;
        if ($mushollaIdsAll->count() > 0) {
            $totalMushollaFacilitiesAll = \DB::table('mosque_facility')
                ->whereIn('mosque_id', $mushollaIdsAll)
                ->where('is_available', 1)
                ->count();
            $avgFacilitiesMushollaTotal = round($totalMushollaFacilitiesAll / $mushollaIdsAll->count(), 2);
        }

        // Percentages out of total facility types
        $facilityTypesTotal = (int) Facility::count();
        $avgFacilitiesMasjidPercentTotal = $facilityTypesTotal > 0 ? round(($avgFacilitiesMasjidTotal / $facilityTypesTotal) * 100, 2) : 0;
        $avgFacilitiesMushollaPercentTotal = $facilityTypesTotal > 0 ? round(($avgFacilitiesMushollaTotal / $facilityTypesTotal) * 100, 2) : 0;

        $summary = [
            'masjid_total' => $masjidTotal,
            'musholla_total' => $mushollaTotal,
            'bkm_total' => $bkmTotal,
            'avg_facilities_masjid_total' => $avgFacilitiesMasjidTotal,
            'avg_facilities_musholla_total' => $avgFacilitiesMushollaTotal,
            'avg_facilities_masjid_percent_total' => $avgFacilitiesMasjidPercentTotal,
            'avg_facilities_musholla_percent_total' => $avgFacilitiesMushollaPercentTotal,
        ];

        // Sample lists to display in facility card
        $masjids = Mosque::with(['witel', 'sto'])
            ->where('type', 'MASJID')
            ->orderBy('name')
            ->take(6)
            ->get();

        $mushollas = Mosque::with(['witel', 'sto'])
            ->where('type', 'MUSHOLLA')
            ->orderBy('name')
            ->take(6)
            ->get();

        // Provinces for facility filter select
        $provinces = Regions::query()
            ->where('type', 'PROVINCE')
            ->orderBy('name')
            ->get();

        // Latest published articles for beranda
        $latestArticles = Article::query()
            ->where('status', 'PUBLISHED')
            ->whereNotNull('published_at')
            ->orderByDesc('published_at')
            ->take(4)
            ->get();

        return view('home.beranda.index', [
            'regions' => $regions,
            'regionals' => $regionals,
            'summary' => $summary,
            'masjids' => $masjids,
            'mushollas' => $mushollas,
            'provinces' => $provinces,
            'latestArticles' => $latestArticles,
        ]);
    }
    
}
