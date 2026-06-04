<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user || (!$user->esAdmin() && !$user->tieneRol($roles))) {
            abort(403, 'No tienes permiso para acceder a este apartado.');
        }

        return $next($request);
    }
}
