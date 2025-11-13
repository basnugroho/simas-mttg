<?php

namespace App\Http\Controllers\Api;

use App\Http\Traits\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
    use ApiResponse;

    /**
 * @OA\Post(
 *     path="/auth/login",
 *     tags={"Auth"},
 *     summary="Login admin",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "password"},
 *             @OA\Property(property="email", type="string", format="email", example="admin@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="secret")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login berhasil",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Login berhasil"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="token", type="string", example="1|xxxxxxxxx")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Email atau password salah")
 * )
 */

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',   // <– pakai username
            'password' => 'required',
        ]);

        /** @var \App\Models\User|null $user */
        $user = User::where('username', $credentials['username'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return $this->error('Username atau password salah', 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return $this->success(['token' => $token], 'Login berhasil');
    }


/**
 * @OA\Post(
 *     path="/auth/logout",
 *     tags={"Auth"},
 *     summary="Logout admin",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Logout berhasil",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Logout berhasil")
 *         )
 *     )
 * )
 */
    public function logout(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $user->currentAccessToken()->delete();

        return $this->success(null, 'Logout berhasil');
    }
}
