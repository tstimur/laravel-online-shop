<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * @param array<int, string> $roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(Response::HTTP_FORBIDDEN);
        }

        if ($roles === []) {
            return $next($request);
        }

        if (!$user->hasAnyRole($roles)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
