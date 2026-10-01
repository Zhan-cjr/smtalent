<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureRoleHrd
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check() || ! Auth::user()->isHrd()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized. Akses khusus HRD/Admin.'], 403);
            }

            return redirect()->route('login')->with('error', 'Akses ditolak. Halaman ini khusus untuk HRD / Admin.');
        }

        return $next($request);
    }
}
