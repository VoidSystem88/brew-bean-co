<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized. Please login.');
        }

        $user = Auth::user();

        // Admin has access to everything
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Check if route is allowed for staff
        $routeName = $request->route()->getName();
        
        // Allow staff to receive transfers
        if ($role === 'admin' && $routeName === 'warehouse.transfer.receive') {
            if ($user->isStaff() || $user->isManager()) {
                return $next($request);
            }
        }

        // Check role
        if ($role === 'staff' && !($user->isStaff() || $user->isManager() || $user->isAdmin())) {
            abort(403, 'Unauthorized action. Staff access required.');
        }

        if ($role === 'manager' && !($user->isManager() || $user->isAdmin())) {
            abort(403, 'Unauthorized action. Manager access required.');
        }

        if ($role === 'admin' && !$user->isAdmin()) {
            abort(403, 'Unauthorized action. Admin access required.');
        }

        return $next($request);
    }
}