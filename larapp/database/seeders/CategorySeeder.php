<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Admin Regional',      'slug' => 'admin-regional'],
            ['name' => 'Redaksi Pusat',       'slug' => 'redaksi-pusat'],
            ['name' => 'Tim Dukungan',        'slug' => 'tim-dukungan'],
            ['name' => 'Kontributor Lapangan','slug' => 'kontributor-lapangan'],
        ];

        foreach ($categories as $c) {
            Category::firstOrCreate(
                ['slug' => $c['slug']],
                ['name' => $c['name'], 'type' => 'ARTICLE']
            );
        }
    }
}
