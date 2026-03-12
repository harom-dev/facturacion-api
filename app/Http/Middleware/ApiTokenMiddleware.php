<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Token de autenticación requerido',
            ], 401);
        }

        $apiToken = ApiToken::with('empresa')
            ->where('token', $token)
            ->first();

        if (!$apiToken) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Token inválido',
            ], 401);
        }

        if (!$apiToken->empresa || !$apiToken->empresa->activa) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Empresa desactivada',
            ], 403);
        }

        // Registrar último uso
        $apiToken->update(['last_used_at' => now()]);

        // Inyectar la empresa en el request para que los controllers la usen
        $request->attributes->set('empresa', $apiToken->empresa);

        return $next($request);
    }
}
