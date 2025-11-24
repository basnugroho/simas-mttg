<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SholatCitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            // JAWA TIMUR
            ['api_id' => '1601', 'name' => 'KAB. BANGKALAN',      'province' => 'JAWA TIMUR'],
            ['api_id' => '1602', 'name' => 'KAB. BANYUWANGI',     'province' => 'JAWA TIMUR'],
            ['api_id' => '1603', 'name' => 'KAB. BLITAR',         'province' => 'JAWA TIMUR'],
            ['api_id' => '1604', 'name' => 'KAB. BOJONEGORO',     'province' => 'JAWA TIMUR'],
            ['api_id' => '1605', 'name' => 'KAB. BONDOWOSO',      'province' => 'JAWA TIMUR'],
            ['api_id' => '1606', 'name' => 'KAB. GRESIK',         'province' => 'JAWA TIMUR'],
            ['api_id' => '1607', 'name' => 'KAB. JEMBER',         'province' => 'JAWA TIMUR'],
            ['api_id' => '1608', 'name' => 'KAB. JOMBANG',        'province' => 'JAWA TIMUR'],
            ['api_id' => '1609', 'name' => 'KAB. KEDIRI',         'province' => 'JAWA TIMUR'],
            ['api_id' => '1610', 'name' => 'KAB. LAMONGAN',       'province' => 'JAWA TIMUR'],
            ['api_id' => '1611', 'name' => 'KAB. LUMAJANG',       'province' => 'JAWA TIMUR'],
            ['api_id' => '1612', 'name' => 'KAB. MADIUN',         'province' => 'JAWA TIMUR'],
            ['api_id' => '1613', 'name' => 'KAB. MAGETAN',        'province' => 'JAWA TIMUR'],
            ['api_id' => '1614', 'name' => 'KAB. MALANG',         'province' => 'JAWA TIMUR'],
            ['api_id' => '1615', 'name' => 'KAB. MOJOKERTO',      'province' => 'JAWA TIMUR'],
            ['api_id' => '1616', 'name' => 'KAB. NGANJUK',        'province' => 'JAWA TIMUR'],
            ['api_id' => '1617', 'name' => 'KAB. NGAWI',          'province' => 'JAWA TIMUR'],
            ['api_id' => '1618', 'name' => 'KAB. PACITAN',        'province' => 'JAWA TIMUR'],
            ['api_id' => '1619', 'name' => 'KAB. PAMEKASAN',      'province' => 'JAWA TIMUR'],
            ['api_id' => '1620', 'name' => 'KAB. PASURUAN',       'province' => 'JAWA TIMUR'],
            ['api_id' => '1621', 'name' => 'KAB. PONOROGO',       'province' => 'JAWA TIMUR'],
            ['api_id' => '1622', 'name' => 'KAB. PROBOLINGGO',    'province' => 'JAWA TIMUR'],
            ['api_id' => '1623', 'name' => 'KAB. SAMPANG',        'province' => 'JAWA TIMUR'],
            ['api_id' => '1624', 'name' => 'KAB. SIDOARJO',       'province' => 'JAWA TIMUR'],
            ['api_id' => '1625', 'name' => 'KAB. SITUBONDO',      'province' => 'JAWA TIMUR'],
            ['api_id' => '1626', 'name' => 'KAB. SUMENEP',        'province' => 'JAWA TIMUR'],
            ['api_id' => '1627', 'name' => 'KAB. TRENGGALEK',     'province' => 'JAWA TIMUR'],
            ['api_id' => '1628', 'name' => 'KAB. TUBAN',          'province' => 'JAWA TIMUR'],
            ['api_id' => '1629', 'name' => 'KAB. TULUNGAGUNG',    'province' => 'JAWA TIMUR'],
            ['api_id' => '1630', 'name' => 'KOTA BATU',           'province' => 'JAWA TIMUR'],
            ['api_id' => '1631', 'name' => 'KOTA BLITAR',         'province' => 'JAWA TIMUR'],
            ['api_id' => '1632', 'name' => 'KOTA KEDIRI',         'province' => 'JAWA TIMUR'],
            ['api_id' => '1633', 'name' => 'KOTA MADIUN',         'province' => 'JAWA TIMUR'],
            ['api_id' => '1634', 'name' => 'KOTA MALANG',         'province' => 'JAWA TIMUR'],
            ['api_id' => '1635', 'name' => 'KOTA MOJOKERTO',      'province' => 'JAWA TIMUR'],
            ['api_id' => '1636', 'name' => 'KOTA PASURUAN',       'province' => 'JAWA TIMUR'],
            ['api_id' => '1637', 'name' => 'KOTA PROBOLINGGO',    'province' => 'JAWA TIMUR'],
            ['api_id' => '1638', 'name' => 'KOTA SURABAYA',       'province' => 'JAWA TIMUR'],

            // BALI
            ['api_id' => '1701', 'name' => 'KAB. BADUNG',         'province' => 'BALI'],
            ['api_id' => '1702', 'name' => 'KAB. BANGLI',         'province' => 'BALI'],
            ['api_id' => '1703', 'name' => 'KAB. BULELENG',       'province' => 'BALI'],
            ['api_id' => '1704', 'name' => 'KAB. GIANYAR',        'province' => 'BALI'],
            ['api_id' => '1705', 'name' => 'KAB. JEMBRANA',       'province' => 'BALI'],
            ['api_id' => '1706', 'name' => 'KAB. KARANGASEM',     'province' => 'BALI'],
            ['api_id' => '1707', 'name' => 'KAB. KLUNGKUNG',      'province' => 'BALI'],
            ['api_id' => '1708', 'name' => 'KAB. TABANAN',        'province' => 'BALI'],
            ['api_id' => '1709', 'name' => 'KOTA DENPASAR',       'province' => 'BALI'],

            // NUSA TENGGARA BARAT (NTB)
            ['api_id' => '1801', 'name' => 'KAB. BIMA',           'province' => 'NTB'],
            ['api_id' => '1802', 'name' => 'KAB. DOMPU',          'province' => 'NTB'],
            ['api_id' => '1803', 'name' => 'KAB. LOMBOK BARAT',   'province' => 'NTB'],
            ['api_id' => '1804', 'name' => 'KAB. LOMBOK TENGAH',  'province' => 'NTB'],
            ['api_id' => '1805', 'name' => 'KAB. LOMBOK TIMUR',   'province' => 'NTB'],
            ['api_id' => '1806', 'name' => 'KAB. LOMBOK UTARA',   'province' => 'NTB'],
            ['api_id' => '1807', 'name' => 'KAB. SUMBAWA',        'province' => 'NTB'],
            ['api_id' => '1808', 'name' => 'KAB. SUMBAWA BARAT',  'province' => 'NTB'],
            ['api_id' => '1809', 'name' => 'KOTA BIMA',           'province' => 'NTB'],
            ['api_id' => '1810', 'name' => 'KOTA MATARAM',        'province' => 'NTB'],

            // NUSA TENGGARA TIMUR (NTT)
            ['api_id' => '1901', 'name' => 'KAB. ALOR',                    'province' => 'NTT'],
            ['api_id' => '1902', 'name' => 'KAB. BELU',                    'province' => 'NTT'],
            ['api_id' => '1903', 'name' => 'KAB. ENDE',                    'province' => 'NTT'],
            ['api_id' => '1904', 'name' => 'KAB. FLORES TIMUR',            'province' => 'NTT'],
            ['api_id' => '1905', 'name' => 'KAB. KUPANG',                  'province' => 'NTT'],
            ['api_id' => '1906', 'name' => 'KAB. LEMBATA',                 'province' => 'NTT'],
            ['api_id' => '1907', 'name' => 'KAB. MALAKA',                  'province' => 'NTT'],
            ['api_id' => '1908', 'name' => 'KAB. MANGGARAI',               'province' => 'NTT'],
            ['api_id' => '1909', 'name' => 'KAB. MANGGARAI BARAT',         'province' => 'NTT'],
            ['api_id' => '1910', 'name' => 'KAB. MANGGARAI TIMUR',         'province' => 'NTT'],
            ['api_id' => '1911', 'name' => 'KAB. NGADA',                   'province' => 'NTT'],
            ['api_id' => '1912', 'name' => 'KAB. NAGEKEO',                 'province' => 'NTT'],
            ['api_id' => '1913', 'name' => 'KAB. ROTE NDAO',               'province' => 'NTT'],
            ['api_id' => '1914', 'name' => 'KAB. SABU RAIJUA',             'province' => 'NTT'],
            ['api_id' => '1915', 'name' => 'KAB. SIKKA',                   'province' => 'NTT'],
            ['api_id' => '1916', 'name' => 'KAB. SUMBA BARAT',             'province' => 'NTT'],
            ['api_id' => '1917', 'name' => 'KAB. SUMBA BARAT DAYA',        'province' => 'NTT'],
            ['api_id' => '1918', 'name' => 'KAB. SUMBA TENGAH',            'province' => 'NTT'],
            ['api_id' => '1919', 'name' => 'KAB. SUMBA TIMUR',             'province' => 'NTT'],
            ['api_id' => '1920', 'name' => 'KAB. TIMOR TENGAH SELATAN',    'province' => 'NTT'],
            ['api_id' => '1921', 'name' => 'KAB. TIMOR TENGAH UTARA',      'province' => 'NTT'],
            ['api_id' => '1922', 'name' => 'KOTA KUPANG',                  'province' => 'NTT'],
        ];

        DB::table('sholat_cities')->insert($cities);
    }
}
