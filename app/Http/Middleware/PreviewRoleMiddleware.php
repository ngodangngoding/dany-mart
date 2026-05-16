<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreviewRoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $activeRole = $request->session()->get('preview_role');

        if (! $activeRole) {
            return redirect()->route('login.preview');
        }

        abort_unless($activeRole === $role, 403);

        return $next($request);
    }
}
