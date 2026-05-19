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

        if (blank($token)) {

            return response()->json([
                'status' => false,
                'message' => 'Token not provided'
            ], 401);
        }

        $expectedToken = config('services.member_api.token');

        if (blank($expectedToken)) {

            return response()->json([
                'status' => false,
                'message' => 'API token not configured'
            ], 500);
        }

        if (!hash_equals($expectedToken, $token)) {

            return response()->json([
                'status' => false,
                'message' => 'Invalid API token'
            ], 401);
        }

        return $next($request);
    }
}
