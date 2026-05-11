<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class SunatController extends Controller
{
    public function ruc(string $numero): JsonResponse
    {
        if (!preg_match('/^\d{11}$/', $numero)) {
            return response()->json(['error' => 'RUC inválido'], 422);
        }

        $response = Http::timeout(8)->get('https://api.apis.net.pe/v1/ruc', [
            'numero' => $numero,
        ]);

        if (!$response->successful()) {
            return response()->json(['error' => 'No encontrado'], 404);
        }

        return response()->json($response->json());
    }
}
