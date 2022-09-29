<?php

namespace App\Http\Middleware;

use Closure;

class ForceToChangePassword
{
    public function handle($request, Closure $next)
    {
        if (config('custom.settings.authentication') == 'database' && empty(auth()->user()->password_changed_at)) {
            return redirect()->route('force.password_change.index');
        }

        return $next($request);
    }
}
