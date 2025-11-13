<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $adminRegional = Category::where('slug', 'admin-regional')->first();
        $redaksiPusat  = Category::where('slug', 'redaksi-pusat')->first();
        $timDukungan   = Category::where('slug', 'tim-dukungan')->first();
        $kontributor   = Category::where('slug', 'kontributor-lapangan')->first();

        $now = Carbon::now();

        $articles = [
            [
                'title'       => 'Peningkatan Aktivitas Keagamaan di Wilayah Timur',
                'category_id' => $adminRegional?->id,
                'summary'     => 'Laporan kegiatan keagamaan yang meningkat di wilayah Jawa Timur, mencakup kajian rutin dan program sosial.',
                'content'     => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nulla facilis, dolore amet...',
                'image_url'   => '/images/info_keagamaan_timur.jpg',
                'published_at'=> $now->copy()->subDays(1),
            ],
            [
                'title'       => 'Program Renovasi Masjid Meningkatkan Kenyamanan',
                'category_id' => $redaksiPusat?->id,
                'summary'     => 'Renovasi beberapa masjid di lingkungan Telkom Regional 3 untuk meningkatkan kenyamanan jamaah.',
                'content'     => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sed dicta, doloremque...',
                'image_url'   => '/images/info_renovasi_masjid.jpg',
                'published_at'=> $now->copy()->subDays(2),
            ],
            [
                'title'       => 'Gotong Royong Warga dalam Pembangunan Musholla Baru',
                'category_id' => $timDukungan?->id,
                'summary'     => 'Kisah inspiratif gotong royong warga dan karyawan membangun musholla baru di Nusa Tenggara.',
                'content'     => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium, consequatur...',
                'image_url'   => '/images/info_gotong_royong.jpg',
                'published_at'=> $now->copy()->subDays(3),
            ],
            [
                'title'       => 'Kegiatan Sosial Bersama BKM di Bulan Ramadhan',
                'category_id' => $kontributor?->id,
                'summary'     => 'Rangkaian kegiatan sosial dan pembagian paket Ramadhan di berbagai masjid dan musholla.',
                'content'     => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. In impedit fugiat...',
                'image_url'   => '/images/info_ramadhan.jpg',
                'published_at'=> $now->copy()->subDays(4),
            ],
        ];

        foreach ($articles as $item) {
            $slug = Str::slug($item['title']);

            Article::firstOrCreate(
                ['slug' => $slug],
                [
                    'title'        => $item['title'],
                    'slug'         => $slug,
                    'category_id'  => $item['category_id'],
                    'summary'      => $item['summary'],
                    'content'      => $item['content'],
                    'image_url'    => $item['image_url'],
                    'published_at' => $item['published_at'],
                    'status'       => 'PUBLISHED',
                ]
            );
        }
    }
}
