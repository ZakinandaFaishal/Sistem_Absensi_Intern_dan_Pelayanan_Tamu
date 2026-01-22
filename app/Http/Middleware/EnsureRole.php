<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        if (($user->active ?? true) === false) {
            abort(403);
        }

        if ($roles === []) {
            abort(403);
        }

        $userRole = $user->role ?? null;

        // Backward-compat: 'admin' was renamed to 'super_admin'.
        if ($userRole === 'admin') {
            $userRole = 'super_admin';
        }

        if (!in_array($userRole, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
