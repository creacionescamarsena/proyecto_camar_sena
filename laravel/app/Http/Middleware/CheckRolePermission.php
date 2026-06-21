<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRolePermission extends Middleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return response()->json([
                'message' => 'No autenticado',
            ], 401);
        }

        if (!in_array($request->user()->rol, $roles)) {
            return response()->json([
                'message' => 'No tienes permiso para acceder a este recurso',
                'requerido' => $roles,
                'actual' => $request->user()->rol,
            ], 403);
        }

        return $next($request);
    }
}
