<?php

namespace Obrainwave\AccessTree\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('accesstree.admin.login');
        }

        return $next($request);
    }
}
