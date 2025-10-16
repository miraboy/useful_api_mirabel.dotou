<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckModuleActive
{
    public function handle($request, Closure $next, $moduleName)
    {
        $user = Auth::user();
        $module = $user->modules()->where('name', $moduleName)->first();

        if (!$module || !$module->pivot->active) {
            return response()->json(['error' => 'Module inactive. Please activate this module to use it.'], 403);
        }

        return $next($request);
    }
}   