<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();
        $userRoles = json_decode($user->role_name, true);
        if (in_array('admin', $userRoles)) {
            return $next($request);
        }
        foreach ($roles as $role) {
            if (in_array($role, $userRoles)) {
                return $next($request);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 403);
    }
}
