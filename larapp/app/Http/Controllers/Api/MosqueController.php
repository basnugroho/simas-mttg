<?php

namespace App\Http\Controllers\Api;

use App\Http\Traits\ApiResponse;
use App\Models\Mosque;
use App\Models\Facility;
use App\Models\MosqueFacility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;

class MosqueController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     *     path="/mosques",
     *     tags={"Mosque"},
     *     summary="List masjid/musholla",
     *     description="Ambil daftar masjid/musholla dengan filter pencarian dan pagination.",
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="search", in="query", description="Cari berdasarkan nama/alamat", @OA\Schema(type="string")),
     *     @OA\Parameter(name="province_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="city_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="witel_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Jenis: MASJID atau MUSHOLLA",
     *         @OA\Schema(type="string", enum={"MASJID","MUSHOLLA"})
     *     ),
     *     @OA\Parameter(
     *         name="min_completion",
     *         in="query",
     *         description="Minimal persentase kelengkapan fasilitas (0-100)",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Urutkan berdasarkan",
     *         @OA\Schema(type="string", enum={"name","completion","newest"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Daftar masjid/musholla",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="items",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="type", type="string"),
     *                         @OA\Property(property="address", type="string", nullable=true),
     *                         @OA\Property(property="province_id", type="integer", nullable=true),
     *                         @OA\Property(property="city_id", type="integer", nullable=true),
     *                         @OA\Property(property="witel_id", type="integer", nullable=true),
     *                         @OA\Property(property="completion_percentage", type="integer"),
     *                         @OA\Property(property="is_active", type="boolean")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="meta",
     *                     type="object",
     *                     @OA\Property(property="current_page", type="integer"),
     *                     @OA\Property(property="per_page", type="integer"),
     *                     @OA\Property(property="total", type="integer"),
     *                     @OA\Property(property="last_page", type="integer")
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    public function index(Request $request)
    {
        $query = Mosque::query()
            ->with(['province', 'city', 'witel']);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($provinceId = $request->query('province_id')) {
            $query->where('province_id', $provinceId);
        }

        if ($cityId = $request->query('city_id')) {
            $query->where('city_id', $cityId);
        }

        if ($witelId = $request->query('witel_id')) {
            $query->where('witel_id', $witelId);
        }

        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }

        if ($minCompletion = $request->query('min_completion')) {
            $query->where('completion_percentage', '>=', $minCompletion);
        }

        $sortBy = $request->query('sort_by', 'name');
        if ($sortBy === 'completion') {
            $query->orderByDesc('completion_percentage');
        } elseif ($sortBy === 'newest') {
            $query->orderByDesc('id');
        } else {
            $query->orderBy('name');
        }

        $perPage = (int) $request->query('per_page', 10);

        $paginator = $query->paginate($perPage);

        $data = [
            'items' => $paginator->items(),
            'meta'  => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
            ],
        ];

        return $this->success($data, 'Daftar masjid/musholla');
    }
    
    /**
 * @OA\Get(
 *     path="/mosques/{id}",
 *     tags={"Mosques"},
 *     summary="Detail masjid/musholla",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Detail masjid/musholla",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="name", type="string"),
 *                 @OA\Property(property="code", type="string", nullable=true),
 *                 @OA\Property(property="type", type="string"),
 *                 @OA\Property(property="address", type="string", nullable=true),
 *                 @OA\Property(property="province_id", type="integer", nullable=true),
 *                 @OA\Property(property="city_id", type="integer", nullable=true),
 *                 @OA\Property(property="witel_id", type="integer", nullable=true),
 *                 @OA\Property(property="latitude", type="number", format="float", nullable=true),
 *                 @OA\Property(property="longitude", type="number", format="float", nullable=true),
 *                 @OA\Property(property="image_url", type="string", nullable=true),
 *                 @OA\Property(property="description", type="string", nullable=true),
 *                 @OA\Property(property="completion_percentage", type="integer"),
 *                 @OA\Property(property="is_active", type="boolean"),
 *                 @OA\Property(
 *                     property="facilities",
 *                     type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="integer"),
 *                         @OA\Property(property="mosque_id", type="integer"),
 *                         @OA\Property(property="facility_id", type="integer"),
 *                         @OA\Property(property="facility_name", type="string"),
 *                         @OA\Property(property="is_available", type="boolean"),
 *                         @OA\Property(property="note", type="string", nullable=true)
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=404, description="Data tidak ditemukan")
 * )
 */
    public function show($id)
    {
        $mosque = Mosque::with([
                'province', 'city', 'witel',
                'mosqueFacility.facility',
            ])->find($id);

        if (! $mosque) {
            return $this->error('Data tidak ditemukan', 404);
        }

        // Bentuk ulang facilities agar mirip schema MosqueFacility Swagger
        $facilities = $mosque->mosqueFacility->map(function ($mf) {
            return [
                'id'            => $mf->id,
                'mosque_id'     => $mf->mosque_id,
                'facility_id'   => $mf->facility_id,
                'facility_name' => $mf->facility->name ?? null,
                'is_available'  => (bool) $mf->is_available,
                'note'          => $mf->note,
            ];
        });

        $mosque->setRelation('facilities_formatted', $facilities);

        return $this->success([
            'id'                     => $mosque->id,
            'name'                   => $mosque->name,
            'code'                   => $mosque->code,
            'type'                   => $mosque->type,
            'address'                => $mosque->address,
            'province_id'            => $mosque->province_id,
            'city_id'                => $mosque->city_id,
            'witel_id'               => $mosque->witel_id,
            'latitude'               => $mosque->latitude,
            'longitude'              => $mosque->longitude,
            'image_url'              => $mosque->image_url,
            'description'            => $mosque->description,
            'completion_percentage'  => $mosque->completion_percentage,
            'is_active'              => $mosque->is_active,
            'facilities'             => $facilities,
        ], 'Detail masjid/musholla');
    }

    /**
 * @OA\Post(
 *     path="/mosques",
 *     tags={"Mosques"},
 *     summary="Tambah masjid/musholla",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","type"},
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="code", type="string", nullable=true),
 *             @OA\Property(property="type", type="string", enum={"MASJID","MUSHOLLA"}),
 *             @OA\Property(property="address", type="string", nullable=true),
 *             @OA\Property(property="province_id", type="integer", nullable=true),
 *             @OA\Property(property="city_id", type="integer", nullable=true),
 *             @OA\Property(property="witel_id", type="integer", nullable=true),
 *             @OA\Property(property="latitude", type="number", format="float", nullable=true),
 *             @OA\Property(property="longitude", type="number", format="float", nullable=true),
 *             @OA\Property(property="image_url", type="string", nullable=true),
 *             @OA\Property(property="description", type="string", nullable=true),
 *             @OA\Property(property="is_active", type="boolean", nullable=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Data berhasil dibuat",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     )
 * )
 */

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:150',
            'code'        => 'nullable|string|max:50',
            'type'        => 'required|in:MASJID,MUSHOLLA',
            'address'     => 'nullable|string|max:255',
            'province_id' => 'nullable|integer|exists:regions,id',
            'city_id'     => 'nullable|integer|exists:regions,id',
            'witel_id'    => 'nullable|integer|exists:regions,id',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
            'image_url'   => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $mosque = Mosque::create($data);

        return $this->success($mosque, 'Data berhasil dibuat', 201);
    }

    /**
 * @OA\Put(
 *     path="/mosques/{id}/facilities",
 *     tags={"Mosques","Facilities"},
 *     summary="Update fasilitas masjid/musholla",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="facilities",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="facility_id", type="integer"),
 *                     @OA\Property(property="is_available", type="boolean"),
 *                     @OA\Property(property="note", type="string", nullable=true)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Fasilitas berhasil diperbarui",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(property="message", type="string")
 *         )
 *     )
 * )
 */

    public function update(Request $request, $id)
    {
        $mosque = Mosque::find($id);
        if (! $mosque) {
            return $this->error('Data tidak ditemukan', 404);
        }

        $data = $request->validate([
            'name'        => 'sometimes|required|string|max:150',
            'code'        => 'nullable|string|max:50',
            'type'        => 'sometimes|required|in:MASJID,MUSHOLLA',
            'address'     => 'nullable|string|max:255',
            'province_id' => 'nullable|integer|exists:regions,id',
            'city_id'     => 'nullable|integer|exists:regions,id',
            'witel_id'    => 'nullable|integer|exists:regions,id',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
            'image_url'   => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $mosque->update($data);

        return $this->success($mosque, 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        $mosque = Mosque::find($id);
        if (! $mosque) {
            return $this->error('Data tidak ditemukan', 404);
        }

        $mosque->delete();

        return response()->noContent();
    }

    public function facilities($id)
    {
        $mosque = Mosque::with('mosqueFacility.facility')->find($id);
        if (! $mosque) {
            return $this->error('Data tidak ditemukan', 404);
        }

        $facilities = $mosque->mosqueFacility->map(function ($mf) {
            return [
                'id'            => $mf->id,
                'mosque_id'     => $mf->mosque_id,
                'facility_id'   => $mf->facility_id,
                'facility_name' => $mf->facility->name ?? null,
                'is_available'  => (bool) $mf->is_available,
                'note'          => $mf->note,
            ];
        });

        return $this->success($facilities, 'Daftar fasilitas');
    }

    public function updateFacilities(Request $request, $id)
    {
        $mosque = Mosque::find($id);
        if (! $mosque) {
            return $this->error('Masjid/musholla tidak ditemukan', 404);
        }

        $validated = $request->validate([
            'facilities'              => 'required|array',
            'facilities.*.facility_id'=> 'required|integer|exists:facilities,id',
            'facilities.*.is_available'=> 'required|boolean',
            'facilities.*.note'       => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($mosque, $validated) {
            // Clear existing
            $mosque->mosqueFacility()->delete();

            // Insert baru
            foreach ($validated['facilities'] as $item) {
                MosqueFacility::create([
                    'mosque_id'    => $mosque->id,
                    'facility_id'  => $item['facility_id'],
                    'is_available' => $item['is_available'],
                    'note'         => $item['note'] ?? null,
                ]);
            }

            // Hitung ulang persentase kelengkapan
            $totalRequired = Facility::where('is_required', true)->count();
            if ($totalRequired > 0) {
                $availableRequired = MosqueFacility::where('mosque_id', $mosque->id)
                    ->where('is_available', true)
                    ->whereHas('facility', function ($q) {
                        $q->where('is_required', true);
                    })
                    ->count();

                $percent = (int) round(($availableRequired / $totalRequired) * 100);
                $mosque->update(['completion_percentage' => $percent]);
            }
        });

        return $this->success(null, 'Fasilitas berhasil diperbarui');
    }
}
