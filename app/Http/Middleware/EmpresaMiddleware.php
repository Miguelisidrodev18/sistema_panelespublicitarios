<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmpresaMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!$user->esEmpresa() && !$user->esAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Acceso denegado.');
        }

        // Empresa users can only access their own empresa data
        if ($user->esEmpresa() && $user->empresa_id === null) {
            return redirect()->route('dashboard')->with('error', 'Tu cuenta no está vinculada a una empresa.');
        }

        return $next($request);
    }
}
