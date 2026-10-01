<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureRoleCandidate
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check() || ! Auth::user()->isCandidate()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized. Akses khusus Kandidat.'], 403);
            }

            return redirect()->route('login')->with('error', 'Akses ditolak. Silakan login sebagai Kandidat.');
        }

        return $next($request);
    }
}
