<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || (!auth()->user()->esAdmin() && !auth()->user()->esGerencia())) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Acceso denegado'], 403);
            }
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
