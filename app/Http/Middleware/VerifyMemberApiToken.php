<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyMemberApiToken
{
    
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'status' => false,
                'message' => 'Token not provided'
            ], 401);
        }

        $expectedToken = config('services.member_api.token');

        if (!$expectedToken) {
            return response()->json([
                'status' => false,
                'message' => 'Server configuration error'
            ], 500);
        }

        if (!hash_equals($expectedToken, $token)) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        return $next($request);
    }
}