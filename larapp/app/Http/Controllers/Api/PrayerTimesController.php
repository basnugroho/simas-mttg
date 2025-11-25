<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PrayerTimesController extends Controller
{
    // Simple endpoint that returns prayer times for given lat/lon and timezone.
    // If timezone is provided as Asia/Jakarta|Asia/Makassar|Asia/Jayapura, controller
    // will return it back so the client can map to WIB/WITA/WIT.
    public function index(Request $request)
    {
        $lat = $request->query('lat');
        $lon = $request->query('lon');
        $tz = $request->query('timezone');

        // Default to Surabaya if no location provided
        if (!$lat || !$lon) {
            // Surabaya coordinates
            $lat = -7.257472; // Surabaya
            $lon = 112.752088;
            if (!$tz) $tz = 'Asia/Jakarta';
        }

        // Use Aladhan public API for prayer times (no API key required).
        // API: https://api.aladhan.com/v1/timings/{timestamp}?latitude={lat}&longitude={lon}&method=2
        $dateStr = now()->format('Y-m-d');
        $cacheKey = "prayertimes:{$lat}:{$lon}:" . ($tz ?: 'local') . ':' . $dateStr;

        $data = Cache::remember($cacheKey, 60 * 24, function () use ($lat, $lon) {
            try {
                $timestamp = time();
                $query = http_build_query([
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'timestamp' => $timestamp,
                    'method' => 2, // University of Islamic Sciences, Karachi (common in ID) — adjust if needed
                ]);
                $url = "https://api.aladhan.com/v1/timings/{$timestamp}?{$query}";
                $opts = [
                    'http' => [
                        'method' => 'GET',
                        'timeout' => 5,
                    ],
                ];
                $context = stream_context_create($opts);
                $resp = @file_get_contents($url, false, $context);
                if(!$resp) return ['error' => 'failed_fetch'];
                $json = json_decode($resp, true);
                if(!$json || ($json['code'] ?? 0) !== 200) return ['error' => 'bad_response'];

                $timings = $json['data']['timings'] ?? [];
                $timezone = $json['data']['meta']['timezone'] ?? ($json['data']['date']['timezone'] ?? null);

                // Map to the keys we use in frontend
                $result = [
                    'date' => $json['data']['date']['gregorian']['date'] ?? now()->format('Y-m-d'),
                    'timezone' => $timezone,
                    'times' => [
                        'subuh' => $timings['Fajr'] ?? null,
                        'dzuhur' => $timings['Dhuhr'] ?? null,
                        'ashar' => $timings['Asr'] ?? null,
                        'maghrib' => $timings['Maghrib'] ?? null,
                        'isya' => $timings['Isha'] ?? null,
                    ],
                ];

                return $result;
            } catch (\Exception $e) {
                return ['error' => 'exception', 'message' => $e->getMessage()];
            }
        });

        return response()->json($data);
    }
}
