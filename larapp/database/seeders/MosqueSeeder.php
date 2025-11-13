<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Mosque;
use App\Models\MosqueFacilitiy;
use App\Models\Regions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MosqueSeeder extends Seeder
{
    public function run(): void
    {
        $jatim = Regions::where('code', 'JATIM')->first();
        $bali  = Regions::where('code', 'BALI')->first();
        $nt    = Regions::where('code', 'NT')->first();

        $witelSurabaya = Regions::where('code', 'WITEL-SBY')->first();
        $witelKediri   = Regions::where('code', 'WITEL-KDR')->first();
        $witelDenpasar = Regions::where('code', 'WITEL-DPS')->first();
        $witelMataram  = Regions::where('code', 'WITEL-MTRM')->first();

        $facility = Facility::pluck('id', 'slug');

        DB::transaction(function () use (
            $jatim, $bali, $nt,
            $witelSurabaya, $witelKediri, $witelDenpasar, $witelMataram,
            $facility
        ) {
            // Masjid-masjid contoh
            $masjid1 = Mosque::create([
                'name'                  => 'Masjid Takhabbur',
                'code'                  => 'MSJ-TKH-01',
                'type'                  => 'MASJID',
                'address'               => 'Kawasan STO Surabaya',
                'province_id'           => $jatim->id,
                'city_id'               => $witelSurabaya->parent_id,
                'witel_id'              => $witelSurabaya->id,
                'latitude'              => -7.2600000,
                'longitude'             => 112.7500000,
                'image_url'             => '/images/masjid_takhabbur.jpg',
                'description'           => 'Masjid utama di lingkungan Telkom Surabaya.',
                'completion_percentage' => 90,
                'is_active'             => true,
            ]);

            $masjid2 = Mosque::create([
                'name'                  => 'Masjid Al-Ikhas',
                'code'                  => 'MSJ-IKH-01',
                'type'                  => 'MASJID',
                'address'               => 'Area Witel Kediri',
                'province_id'           => $jatim->id,
                'city_id'               => $witelKediri->parent_id,
                'witel_id'              => $witelKediri->id,
                'latitude'              => -7.8169000,
                'longitude'             => 112.0113000,
                'image_url'             => '/images/masjid_al_ikhas.jpg',
                'description'           => 'Masjid dengan fasilitas lengkap untuk jamaah karyawan.',
                'completion_percentage' => 75,
                'is_active'             => true,
            ]);

            $masjid3 = Mosque::create([
                'name'                  => 'Masjid Al-Muttaqin',
                'code'                  => 'MSJ-MTQ-01',
                'type'                  => 'MASJID',
                'address'               => 'Denpasar',
                'province_id'           => $bali->id,
                'city_id'               => $witelDenpasar->parent_id,
                'witel_id'              => $witelDenpasar->id,
                'latitude'              => -8.6500000,
                'longitude'             => 115.2167000,
                'image_url'             => '/images/masjid_al_muttaqin.jpg',
                'description'           => 'Masjid pusat kegiatan rohani Telkom Regionsal Bali.',
                'completion_percentage' => 65,
                'is_active'             => true,
            ]);

            $musholla1 = Mosque::create([
                'name'                  => 'Musholla Nurul Huda',
                'code'                  => 'MSH-NRL-01',
                'type'                  => 'MUSHOLLA',
                'address'               => 'Kantor Telkom Mataram',
                'province_id'           => $nt->id,
                'city_id'               => $witelMataram->parent_id,
                'witel_id'              => $witelMataram->id,
                'latitude'              => -8.5833000,
                'longitude'             => 116.1167000,
                'image_url'             => '/images/musholla_nurul_huda.jpg',
                'description'           => 'Musholla nyaman untuk karyawan dan pengunjung.',
                'completion_percentage' => 82,
                'is_active'             => true,
            ]);

            $musholla2 = Mosque::create([
                'name'                  => 'Musholla Al-Falah',
                'code'                  => 'MSH-FLH-01',
                'type'                  => 'MUSHOLLA',
                'address'               => 'Area STO Kediri',
                'province_id'           => $jatim->id,
                'city_id'               => $witelKediri->parent_id,
                'witel_id'              => $witelKediri->id,
                'latitude'              => -7.8165000,
                'longitude'             => 112.0160000,
                'image_url'             => '/images/musholla_al_falah.jpg',
                'description'           => 'Musholla kecil dengan kegiatan kajian rutin.',
                'completion_percentage' => 68,
                'is_active'             => true,
            ]);

            // Helper buat assign fasilitas ke satu masjid
            $assignFacilitiy = function (Mosque $mosque, array $slugs) use ($facility) {
                foreach ($slugs as $slug) {
                    if (! isset($facilitiy[$slug])) {
                        continue;
                    }

                    MosqueFacility::create([
                        'mosque_id'    => $mosque->id,
                        'facility_id'  => $facility[$slug],
                        'is_available' => true,
                        'note'         => null,
                    ]);
                }
            };

            $assignFacilitiy($masjid1, [
                'tempat-wudhu', 'toilet', 'parkir', 'sound-system',
                'karpet', 'ac-ventilasi', 'wudhu-wanita', 'ruang-taklim',
            ]);

            $assignFacilitiy($masjid2, [
                'tempat-wudhu', 'toilet', 'parkir', 'karpet', 'wudhu-wanita',
            ]);

            $assignFacilitiy($masjid3, [
                'tempat-wudhu', 'toilet', 'parkir', 'karpet',
            ]);

            $assignFacilitiy($musholla1, [
                'tempat-wudhu', 'toilet', 'karpet', 'ruang-taklim',
            ]);

            $assignFacilitiy($musholla2, [
                'tempat-wudhu', 'toilet', 'karpet',
            ]);
        });
    }
}
