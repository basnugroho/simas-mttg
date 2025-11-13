<?php

namespace Database\Seeders;

use App\Models\PrayerTimes;
use App\Models\Regions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PrayerTimeSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today()->toDateString();

        $jatim = Regions::where('code', 'JATIM')->first();
        $bali  = Regions::where('code', 'BALI')->first();
        $nt    = Regions::where('code', 'NT')->first();

        $data = [
            [
                'region_id' => $jatim?->id,
                'date'      => $today,
                'subuh'     => '03:43',
                'dzuhur'    => '11:46',
                'ashar'     => '14:31',
                'maghrib'   => '17:27',
                'isya'      => '18:39',
            ],
            [
                'region_id' => $bali?->id,
                'date'      => $today,
                'subuh'     => '04:10',
                'dzuhur'    => '12:00',
                'ashar'     => '15:20',
                'maghrib'   => '18:10',
                'isya'      => '19:15',
            ],
            [
                'region_id' => $nt?->id,
                'date'      => $today,
                'subuh'     => '04:20',
                'dzuhur'    => '12:05',
                'ashar'     => '15:25',
                'maghrib'   => '18:15',
                'isya'      => '19:20',
            ],
        ];

        foreach ($data as $row) {
            PrayerTimes::updateOrCreate(
                ['region_id' => $row['region_id'], 'date' => $row['date']],
                $row
            );
        }
    }
}
