<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mosque;
use App\Models\Regions;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Provinces of interest
        $provinces = [
            'jawa_timur' => 'Jawa Timur',
            'bali' => 'Bali',
            'nusa_tenggara' => 'Nusa Tenggara',
        ];

        $summary = ['total' => ['masjid' => 0, 'mushalla' => 0]];
        $labels = [];
        $masjidTotals = [];
        $masjidComplete = [];
        $mushollaTotals = [];
        $mushollaComplete = [];

        foreach ($provinces as $key => $name) {
            $labels[] = $name;

            $provinceMosques = Mosque::whereHas('area', function($q) use ($name) {
                $q->where('name', 'like', "%{$name}%");
            });

            // treat type 'mushalla' as mushalla, everything else as masjid
            $mushallaCount = (clone $provinceMosques)->whereRaw("LOWER(COALESCE(type, 'MUSHOLLA')) = 'musholla'")->count();
            $masjidCount = (clone $provinceMosques)->whereRaw("LOWER(COALESCE(type, 'masjid')) != 'musholla'")->count();

            $masjidTotals[] = $masjidCount;
            $mushollaTotals[] = $mushallaCount;

            // 'Lengkap' defined as completion_percentage >= 90
            $masjidComplete[] = (clone $provinceMosques)->whereRaw("LOWER(COALESCE(type,'masjid')) != 'mushalla'")->where('completion_percentage', '>=', 90)->count();
            $mushollaComplete[] = (clone $provinceMosques)->whereRaw("LOWER(COALESCE(type,'masjid')) = 'mushalla'")->where('completion_percentage', '>=', 90)->count();

            $summary[$key] = [
                'masjid' => $masjidCount,
                'mushalla' => $mushallaCount,
            ];

            $summary['total']['masjid'] += $masjidCount;
            $summary['total']['mushalla'] += $mushallaCount;
        }

        // Overall 'Keseluruhan' labels appended
        $labels[] = 'Keseluruhan';
        $masjidTotals[] = $summary['total']['masjid'];
        $mushollaTotals[] = $summary['total']['mushalla'];
        $masjidComplete[] = Mosque::whereRaw("LOWER(COALESCE(type,'masjid')) != 'mushalla'")->where('completion_percentage', '>=', 90)->count();
        $mushollaComplete[] = Mosque::whereRaw("LOWER(COALESCE(type,'masjid')) = 'mushalla'")->where('completion_percentage', '>=', 90)->count();

        // Build Chart.js datasets
        $barData = [
            'labels' => $labels,
            'datasets' => [
                [ 'label' => 'Masjid (Total)', 'backgroundColor' => '#1f77b4', 'data' => $masjidTotals ],
                [ 'label' => 'Masjid (Lengkap)', 'backgroundColor' => '#17a2b8', 'data' => $masjidComplete ],
                [ 'label' => 'Musholla (Total)', 'backgroundColor' => '#6c757d', 'data' => $mushollaTotals ],
                [ 'label' => 'Musholla (Lengkap)', 'backgroundColor' => '#dc3545', 'data' => $mushollaComplete ],
            ],
        ];

        // Stacked percentage chart for facility completeness per area
        $requiredCount = \App\Models\Facility::where('is_required', true)->count();
        $masjidFacilityPercent = [];
        $mushollaFacilityPercent = [];

        // compute for each province label (exclude the final 'Keseluruhan' for the loop)
        $provinceLabels = array_slice($labels, 0, count($provinces));
        foreach ($provinceLabels as $label) {
            $provinceName = $label;
            $provinceQuery = Mosque::whereHas('area', function($q) use ($provinceName) {
                $q->where('name', 'like', "%{$provinceName}%");
            });

            $masjidMosques = (clone $provinceQuery)->whereRaw("LOWER(COALESCE(type,'masjid')) != 'mushalla'")->get();
            $mushollaMosques = (clone $provinceQuery)->whereRaw("LOWER(COALESCE(type,'masjid')) = 'mushalla'")->get();

            $calcAvg = function($collection) use ($requiredCount) {
                if ($requiredCount <= 0 || $collection->count() === 0) return 0;
                $sum = 0;
                foreach ($collection as $m) {
                    $available = $m->mosqueFacility()->whereHas('facility', function($q){ $q->where('is_required', true); })->where('is_available', true)->count();
                    $sum += ($available / $requiredCount) * 100;
                }
                return round($sum / $collection->count(), 1);
            };

            $masjidFacilityPercent[] = $calcAvg($masjidMosques);
            $mushollaFacilityPercent[] = $calcAvg($mushollaMosques);
        }

        // Keseluruhan (overall)
        if ($requiredCount <= 0) {
            $masjidFacilityPercent[] = 0;
            $mushollaFacilityPercent[] = 0;
        } else {
            $allMasjid = Mosque::whereRaw("LOWER(COALESCE(type,'masjid')) != 'mushalla'")->get();
            $allMusholla = Mosque::whereRaw("LOWER(COALESCE(type,'masjid')) = 'mushalla'")->get();
            $masjidFacilityPercent[] = $allMasjid->count() ? round(array_sum($allMasjid->map(function($m) use ($requiredCount){
                return ($m->mosqueFacility()->whereHas('facility', function($q){ $q->where('is_required', true); })->where('is_available', true)->count() / $requiredCount) * 100;
            })->toArray()) / $allMasjid->count(), 1) : 0;
            $mushollaFacilityPercent[] = $allMusholla->count() ? round(array_sum($allMusholla->map(function($m) use ($requiredCount){
                return ($m->mosqueFacility()->whereHas('facility', function($q){ $q->where('is_required', true); })->where('is_available', true)->count() / $requiredCount) * 100;
            })->toArray()) / $allMusholla->count(), 1) : 0;
        }

        $stackFacilitiesData = [
            'labels' => $labels,
            'datasets' => [
                [ 'label' => 'Masjid Lengkap (%)', 'backgroundColor' => '#1B3C53', 'data' => $masjidFacilityPercent, 'stack' => 'masjid' ],
                [ 'label' => 'Musholla Lengkap (%)', 'backgroundColor' => '#DC0000', 'data' => $mushollaFacilityPercent, 'stack' => 'musholla' ],
            ],
        ];

        // Area pie data: aggregate counts of Masjid and Musholla per area (regions.name)
        $areaRows = DB::table('mosques as a')
            ->leftJoin('regions as b', 'b.id', '=', 'a.area_id')
            ->select('b.name',
                DB::raw("COUNT(case when LOWER(COALESCE(a.type,'masjid')) != 'musholla' then 1 end) as total_masjid"),
                DB::raw("COUNT(case when LOWER(COALESCE(a.type,'masjid')) = 'musholla' then 1 end) as total_musholla")
            )
            ->groupBy('b.name')
            ->get();

        $areaLabels = $areaRows->pluck('name')->map(function($v){ return $v ?? 'Unknown'; })->toArray();
        $areaMasjid = $areaRows->pluck('total_masjid')->map(function($v){ return (int)$v; })->toArray();
        $areaMusholla = $areaRows->pluck('total_musholla')->map(function($v){ return (int)$v; })->toArray();

        $areaPieData = [
            'labels' => $areaLabels,
            'datasets' => [
                [ 'label' => 'Masjid', 'data' => $areaMasjid, 'backgroundColor' => array_map(function($i){ return ['#1f77b4','#2ca02c','#ff7f0e','#9467bd','#8c564b','#e377c2','#7f7f7f','#bcbd22','#17becf'][$i % 9]; }, array_keys($areaLabels)), ],
                [ 'label' => 'Musholla', 'data' => $areaMusholla, 'backgroundColor' => array_map(function($i){ return ['#ff6384','#ffa600','#ffcd56','#4dc9f6','#f67019','#f53794','#537bc4','#acc236','#166a8f'][$i % 9]; }, array_keys($areaLabels)), ],
            ],
        ];

        $donutMasjid = [
            'labels' => array_values($provinces),
            'datasets' => [[ 'data' => array_slice($masjidTotals,0, count($provinces)), 'backgroundColor' => ['#1f77b4','#2ca02c','#ff7f0e'] ]]
        ];

        $donutMusholla = [
            'labels' => array_values($provinces),
            'datasets' => [[ 'data' => array_slice($mushollaTotals,0, count($provinces)), 'backgroundColor' => ['#ff6384','#ffa600','#ffcd56'] ]]
        ];

        // Incomplete list: lowest completion_percentage, limit 12 (eager-load area to avoid N+1)
        $incompleteList = Mosque::with(['area'])->where(function($q){
                $q->whereNull('completion_percentage')->orWhere('completion_percentage', '<', 100);
            })->orderBy('completion_percentage', 'asc')->limit(12)->get();

        // Map points: mosques with lat/lng
        $mapPoints = Mosque::whereNotNull('latitude')->whereNotNull('longitude')
            ->select(['id','name','latitude','longitude','completion_percentage'])
            ->limit(1000)->get()->map(function($m){
                return [
                    'id' => $m->id,
                    'name' => $m->name,
                    'lat' => (float)$m->latitude,
                    'lng' => (float)$m->longitude,
                    'popup' => $m->name . ' (' . ((int)($m->completion_percentage ?? 0)) . '%)'
                ];
            })->toArray();

        return view('administrator.dashboard.main', compact(
            'summary','barData','donutMasjid','donutMusholla','incompleteList','mapPoints','stackFacilitiesData','areaPieData','provinces'
        ));
    }
}
