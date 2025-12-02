<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return redirect()->route('login');
        }

        if (auth()->user()->role !== $role) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Forbidden - Insufficient permissions'], 403);
            }
            // Redirect to appropriate dashboard based on user's actual role
            $userRole = auth()->user()->role;
            if ($userRole === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($userRole === 'doctor') {
                return redirect()->route('doctor.dashboard');
            } elseif ($userRole === 'patient') {
                return redirect()->route('patient.dashboard');
            }
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
