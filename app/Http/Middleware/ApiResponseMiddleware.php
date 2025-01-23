<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class ApiResponseMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $response = $next($request);

            // If the response is not JSON, convert it
            if (!$response instanceof JsonResponse) {
                if ($response->getStatusCode() === 401) {
                    $response = response()->json([
                        'status' => 'error',
                        'message' => 'Unauthorized access',
                        'code' => 401
                    ], 401);
                } elseif ($response->getStatusCode() === 403) {
                    $response = response()->json([
                        'status' => 'error',
                        'message' => 'Forbidden access',
                        'code' => 403
                    ], 403);
                } elseif ($response->getStatusCode() === 404) {
                    $response = response()->json([
                        'status' => 'error',
                        'message' => 'Resource not found',
                        'code' => 404
                    ], 404);
                } elseif ($response->getStatusCode() >= 500) {
                    Log::error('Internal server error in response', [
                        'url' => $request->fullUrl(),
                        'method' => $request->method(),
                        'status' => $response->getStatusCode()
                    ]);
                    
                    $response = response()->json([
                        'status' => 'error',
                        'message' => 'Internal server error',
                        'code' => 500,
                        'debug' => config('app.debug') ? [
                            'url' => $request->fullUrl(),
                            'method' => $request->method()
                        ] : null
                    ], 500);
                }
            }

            // Add common headers
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-XSS-Protection', '1; mode=block');
            $response->headers->set('X-Frame-Options', 'DENY');
            
            // Handle CORS headers
            $origin = $request->header('Origin');
            if ($origin && in_array($origin, config('cors.allowed_origins'))) {
                $response->headers->set('Access-Control-Allow-Origin', $origin);
            }
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization, X-Request-With');

            return $response;

        } catch (\Exception $e) {
            Log::error('Exception in API middleware', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'url' => $request->fullUrl(),
                'method' => $request->method()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error',
                'code' => 500,
                'debug' => config('app.debug') ? [
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'url' => $request->fullUrl(),
                    'method' => $request->method()
                ] : null
            ], 500);
        }
    }
} 