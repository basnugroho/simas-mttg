<?php

namespace App\Http\Controllers\Api;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="SIMAS MTTG API",
 *     version="1.0.0",
 *     description="Backend API untuk Sistem Informasi Manajemen Masjid (SIMAS) Majelis Taklim Telkom Group."
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * 
* @OA\Tag(
 *     name="Auth",
 *     description="Autentikasi admin / pengguna"
 * )
 * 
 * @OA\Tag(
 *     name="Regions",
 *     description="Master wilayah (provinsi, kota, witel)"
 * )
* @OA\Tag(
 *     name="Mosques",
 *     description="Manajemen data masjid dan musholla"
 * )
  * @OA\Tag(
 *     name="Facility",
 *     description="Master fasilitas dan fasilitas per-masjid"
 * )
 */
class SwaggerController extends Controller
{
    // Tidak perlu ada method di sini, ini hanya untuk anotasi Swagger saja.
}
