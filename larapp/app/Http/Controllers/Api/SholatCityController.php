<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class SholatCityController extends BaseController
{
    /**
     * GET /api/sholat-cities?q=surabaya
     * Returns [{id, name, province, api_id, tz}]
     */
    public function index(Request $request)
    {
        $q = trim((string)$request->query('q', ''));

        $cities = DB::table('sholat_cities')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function($w) use ($q){
                    $w->where('name', 'like', '%' . $q . '%')
                      ->orWhere('province', 'like', '%' . $q . '%');
                });
            })
            ->orderBy('province')
            ->orderBy('name')
            ->limit((int)min(max((int)$request->query('limit', 20), 1), 50))
            ->get(['id', 'api_id', 'name', 'province']);

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
