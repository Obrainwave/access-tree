<?php
namespace Obrainwave\AccessTree\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAccessMiddleware
{
    public function handle(Request $request, Closure $next, ...$abilities)
    {
        $user = auth()->user();

        if (! $user) {
            return $this->deny($request);
        }

        foreach ($abilities as $ability) {
            // If ability starts with "role:", check role
            if (str_starts_with($ability, 'role:')) {
                $roleSlug = substr($ability, 5);
                if (checkRoles([$roleSlug])) {
                    return $next($request);
                }
            } else {
                // Default: treat as permission
                if (checkPermission($ability)) {
                    return $next($request);
                }
            }
        }

        return $this->deny($request);
    }

    protected function deny(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Fallback route from config
        $fallbackRoute = config('accesstree.forbidden_redirect', 'home');

        // Avoid redirect loop
        $previous = url()->previous();
        $current  = $request->fullUrl();

        if ($previous === $current) {
            return redirect()->route($fallbackRoute)
                ->with('danger', 'Access Forbidden');
        }

        return redirect()->back()->with('danger', 'Access Forbidden');
    }
}

