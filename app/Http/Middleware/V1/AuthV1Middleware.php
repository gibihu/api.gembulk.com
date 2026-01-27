<?php

namespace App\Http\Middleware\V1;

use App\Models\Users\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthV1Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // ดึง API Key จาก Header
        $apiKey = $request->header('X-API-KEY');

        // ไม่ส่ง key มา
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API key is required',
                'code' => 401
            ], 401);
        }

        // หา key ใน DB
        $validKey = ApiKey::where('token', $apiKey)->first();

        // ไม่เจอ key
        if (!$validKey) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key',
                'code' => 403,
            ], 403);
        }

        // (เสริม) ถ้าอยากส่ง user id ต่อไปใช้
        $request->merge([
            'api_key_id' => $validKey->id,
        ]);

        return $next($request);
    }
}
