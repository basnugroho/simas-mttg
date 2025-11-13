<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        $facilities = [
            ['name' => 'Tempat Wudhu',             'slug' => 'tempat-wudhu',            'is_required' => true],
            ['name' => 'Toilet',                   'slug' => 'toilet',                  'is_required' => true],
            ['name' => 'Area Parkir',              'slug' => 'parkir',                  'is_required' => true],
            ['name' => 'Sound System',             'slug' => 'sound-system',            'is_required' => false],
            ['name' => 'Karpet / Sajadah',         'slug' => 'karpet',                  'is_required' => true],
            ['name' => 'AC / Ventilasi Baik',      'slug' => 'ac-ventilasi',            'is_required' => false],
            ['name' => 'Tempat Wudhu Wanita',      'slug' => 'wudhu-wanita',            'is_required' => true],
            ['name' => 'Ruang Taklim / Kajian',    'slug' => 'ruang-taklim',            'is_required' => false],
            ['name' => 'Perpustakaan / Rak Buku',  'slug' => 'perpustakaan',            'is_required' => false],
            ['name' => 'Akses Difabel',            'slug' => 'akses-difabel',           'is_required' => false],
        ];

        foreach ($facilities as $f) {
            Facility::firstOrCreate(
                ['slug' => $f['slug']],
                $f
            );
        }
    }
}
