<?php

namespace App\Http\Controllers\Api;

use App\Http\Traits\ApiResponse;
use App\Models\Regions;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class RegionController extends Controller
{
    use ApiResponse;
    
    /**
     * @OA\Get(
     *     path="/regions",
     *     tags={"Regions"},
     *     summary="Daftar wilayah",
     *     @OA\Parameter(
     *         name="level",
     *         in="query",
     *         required=false,
     *         description="REGIONAL / AREA / WITEL / STO",
     *         @OA\Schema(type="string", enum={"REGIONAL","AREA","WITEL","STO"})
     *     ),
     *     @OA\Parameter(
     *         name="parent_id",
     *         in="query",
         *         required=false,
     *         description="Filter berdasarkan parent region",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Daftar wilayah",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="level", type="string"),
     *                     @OA\Property(property="level_label", type="string", nullable=true),
     *                     @OA\Property(property="code", type="string", nullable=true),
     *                     @OA\Property(property="parent_id", type="integer", nullable=true)
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Regions::query();

        // Filter strictly by level; deprecate legacy type/type_key
        if ($level = $request->query('level')) {
            $query->where('level', $level);
        }

        if ($parentId = $request->query('parent_id')) {
            $query->where('parent_id', $parentId);
        }

        $regions = $query->orderBy('name')->get();

        // Transform output: expose level and human label; omit type/type_key
        $out = $regions->map(function($r) {
            return [
                'id' => $r->id,
                'name' => $r->name,
                'level' => $r->level,
                'level_label' => method_exists($r, 'displayLevelLabel') ? $r->displayLevelLabel() : ($r->level ?? null),
                'code' => $r->code,
                'parent_id' => $r->parent_id,
                'pov' => $r->pov ?? null,
            ];
        });

        return $this->success($out, 'Daftar wilayah');
    }
}
