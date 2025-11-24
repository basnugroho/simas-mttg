<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SholatCityController extends Controller
{
    /**
     * Search sholat_cities by name (simple autocomplete)
     */
    public function search(Request $request)
    {
        $q = $request->query('q', '');

        $cities = DB::table('sholat_cities')
            ->when($q !== '', function ($query) use ($q) {
                $query->where('name', 'like', '%' . $q . '%');
            })
            ->orderBy('province')
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'api_id', 'name', 'province']);

        // attach timezone label based on province or api_id prefix
        $mapped = $cities->map(function($c){
            $prov = strtoupper($c->province ?? '');
            $tz = 'WIB';

            if(strpos($prov, 'MALUKU') !== false || strpos($prov, 'PAPUA') !== false){
                $tz = 'WIT';
            } elseif(strpos($prov, 'BALI') !== false || strpos($prov, 'NTB') !== false || strpos($prov, 'NTT') !== false || strpos($prov, 'SULAWESI') !== false) {
                $tz = 'WITA';
            } else {
                $pid = (string)($c->api_id ?? '');
                if(strlen($pid) >= 2){
                    $prefix = substr($pid,0,2);
                    if(in_array($prefix, ['17','18','19'])) $tz = 'WITA';
                    if(in_array($prefix, ['20','21','22'])) $tz = 'WIT';
                }
            }

            return [
                'id' => $c->id,
                'api_id' => $c->api_id,
                'name' => $c->name,
                'province' => $c->province,
                'tz' => $tz,
            ];
        });

        return response()->json($mapped->values());
    }
}
