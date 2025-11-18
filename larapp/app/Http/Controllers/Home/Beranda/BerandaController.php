<?php
    
namespace App\Http\Controllers\Home\Beranda;

use App\Http\Controllers\Controller;
use App\Models\Regions;
use App\Models\Mosque;
use App\Models\Article;

class BerandaController extends Controller
{
    public function index()
    {
        // Fetch a few regions as example (top-level provinces) with counts
        $regions = Regions::query()
            ->where('type', 'PROVINCE')
            ->withCount([
                'mosques as masjid_count' => function ($q) { $q->where('type', 'MASJID'); },
                'mosques as musholla_count' => function ($q) { $q->where('type', 'MUSHOLLA'); },
                // placeholder for BKM if stored in mosques table with type 'BKM'
                'mosques as bkm_count' => function ($q) { $q->where('type', 'BKM'); },
                // completed counts per region
                'mosques as complete_masjid_count' => function ($q) { $q->where('type', 'MASJID')->where('completion_percentage', '>=', 100); },
                'mosques as complete_musholla_count' => function ($q) { $q->where('type', 'MUSHOLLA')->where('completion_percentage', '>=', 100); },
            ])
            ->orderBy('name')
            ->take(3)
            ->get();

        // Build a summary from Mosques
        $masjidTotal = Mosque::where('type', 'MASJID')->count();
        $mushollaTotal = Mosque::where('type', 'MUSHOLLA')->count();
        $bkmTotal = 0; // placeholder until BKM model exists

        $summary = [
            'masjid_total' => $masjidTotal,
            'musholla_total' => $mushollaTotal,
            'bkm_total' => $bkmTotal,
            'complete_masjid_total' => Mosque::where('type', 'MASJID')->where('completion_percentage', '>=', 100)->count(),
            'complete_musholla_total' => Mosque::where('type', 'MUSHOLLA')->where('completion_percentage', '>=', 100)->count(),
        ];

        // Sample lists to display in facility card
        $masjids = Mosque::with(['province', 'city'])
            ->where('type', 'MASJID')
            ->orderBy('name')
            ->take(6)
            ->get();

        $mushollas = Mosque::with(['province', 'city'])
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
            ->take(8)
            ->get();

        return view('home.beranda.index', [
            'regions' => $regions,
            'summary' => $summary,
            'masjids' => $masjids,
            'mushollas' => $mushollas,
            'provinces' => $provinces,
            'latestArticles' => $latestArticles,
        ]);
    }
    
}
