<?php

namespace Database\Seeders;

use App\Models\Regions;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        // Provinces
        $jatim = Regions::create([
            'name' => 'Jawa Timur',
            'type' => 'PROVINCE',
            'code' => 'JATIM',
        ]);

        $bali = Regions::create([
            'name' => 'Bali',
            'type' => 'PROVINCE',
            'code' => 'BALI',
        ]);

        $nt = Regions::create([
            'name' => 'Nusa Tenggara',
            'type' => 'PROVINCE',
            'code' => 'NT',
        ]);

        // Cities
        $surabaya = Regions::create([
            'name'      => 'Kota Surabaya',
            'type'      => 'CITY',
            'code'      => 'SBY',
            'parent_id' => $jatim->id,
        ]);

        $kediri = Regions::create([
            'name'      => 'Kediri',
            'type'      => 'CITY',
            'code'      => 'KDR',
            'parent_id' => $jatim->id,
        ]);

        $denpasar = Regions::create([
            'name'      => 'Denpasar',
            'type'      => 'CITY',
            'code'      => 'DPS',
            'parent_id' => $bali->id,
        ]);

        $mataram = Regions::create([
            'name'      => 'Mataram',
            'type'      => 'CITY',
            'code'      => 'MTRM',
            'parent_id' => $nt->id,
        ]);

        // Witel (contoh)
        Regions::create([
            'name'      => 'Witel Surabaya',
            'type'      => 'WITEL',
            'code'      => 'WITEL-SBY',
            'parent_id' => $surabaya->id,
        ]);

        Regions::create([
            'name'      => 'Witel Kediri',
            'type'      => 'WITEL',
            'code'      => 'WITEL-KDR',
            'parent_id' => $kediri->id,
        ]);

        Regions::create([
            'name'      => 'Witel Denpasar',
            'type'      => 'WITEL',
            'code'      => 'WITEL-DPS',
            'parent_id' => $denpasar->id,
        ]);

        Regions::create([
            'name'      => 'Witel Mataram',
            'type'      => 'WITEL',
            'code'      => 'WITEL-MTRM',
            'parent_id' => $mataram->id,
        ]);
    }
}
