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
     *         name="type",
     *         in="query",
     *         required=false,
     *         description="PROVINCE / CITY / WITEL / OTHER",
     *         @OA\Schema(type="string", enum={"PROVINCE","CITY","WITEL","OTHER"})
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
     *                     @OA\Property(property="type", type="string"),
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

        if ($type = $request->query('type')) {
            // Accept either legacy type or canonical type_key: match either column
            $query->where(function($q) use ($type) {
                $q->where('type_key', $type)
                  ->orWhere('type', $type);
            });
        }

        if ($parentId = $request->query('parent_id')) {
            $query->where('parent_id', $parentId);
        }

        $regions = $query->orderBy('name')->get();

        // Transform output: include canonical type_key and human label
        $out = $regions->map(function($r) {
            return [
                'id' => $r->id,
                'name' => $r->name,
                'type' => $r->type_key ?? $r->type,
                'type_label' => method_exists($r, 'displayTypeLabel') ? $r->displayTypeLabel() : ($r->type_key ? (Regions::TYPES[$r->type_key] ?? $r->type_key) : $r->type),
                'code' => $r->code,
                'parent_id' => $r->parent_id,
                'pov' => $r->pov ?? null,
            ];
        });

        return $this->success($out, 'Daftar wilayah');
    }
}
