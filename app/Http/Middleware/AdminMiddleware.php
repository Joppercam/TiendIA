<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Verificar si el usuario está autenticado y tiene rol admin o super-admin
        if (Auth::check() && Auth::user()->hasAnyRole(['admin', 'super-admin'])) {
            return $next($request);
        }
        
        // Redireccionar a inicio si no tiene permisos
        return redirect()->route('home')->with('error', 'No tienes permisos para acceder al panel de administración.');
    }
}