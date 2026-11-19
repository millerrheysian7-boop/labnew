<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RestrictUserRole
{
    /**
     * Plain "user" accounts may only view their dashboard.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && ! in_array(Auth::user()->role, ['admin', 'staff'], true)) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
