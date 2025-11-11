<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

class BitcoinController extends Controller
{
    /**
     * Get current Bitcoin price in IDR
     *
     * @OA\Get(
     *     path="/api/bitcoin-price",
     *     tags={"Tes API"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="ticker", type="object",
     *                 @OA\Property(property="buy", type="string"),
     *                 @OA\Property(property="high", type="string"),
     *                 @OA\Property(property="last", type="string"),
     *                 @OA\Property(property="low", type="string"),
     *                 @OA\Property(property="sell", type="string"),
     *                 @OA\Property(property="server_time", type="integer"),
     *                 @OA\Property(property="vol_btc", type="string"),
     *                 @OA\Property(property="vol_idr", type="string")
     *             )
     *         )
     *     )
     * )
     */
    public function getPrice()
    {
        // Ambil harga Bitcoin dari API eksternal (Indodax)
        // Correct endpoint: https://indodax.com/api/btc_idr/ticker
        $response = Http::get('https://indodax.com/api/btc_idr/ticker');
        if ($response->ok()) {
            $data = $response->json();
            return response()->json($data);
        }
        return response()->json(['error' => 'Failed to fetch price'], 500);
    }
}
