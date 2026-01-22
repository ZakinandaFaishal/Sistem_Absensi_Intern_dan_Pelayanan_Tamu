<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminDinasHasDinas
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        if (($user->role ?? null) === 'admin_dinas' && empty($user->dinas_id)) {
            abort(403);
        }

        return $next($request);
    }
}
