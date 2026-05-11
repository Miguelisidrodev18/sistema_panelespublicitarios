<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permiso): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->tienePermiso($permiso)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Permiso denegado'], 403);
            }
            return redirect()->route('dashboard')->with('error', 'No tienes permiso para acceder a: ' . $permiso);
        }

        return $next($request);
    }
}
