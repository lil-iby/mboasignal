<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    public function handle($request, Closure $next)
    {
        return $next($request); // supprime toute vérification (risqué)
    }
    protected $except = [
        'api/*',         // toutes les routes commençant par /api
        'auth/*',        // ou les routes spécifiques d’auth
    ];
}

