<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckingUserActiveOrNot
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->user()->is_active == 0) {
            Auth::logout();
            return redirect('login')->with(['message' => 'Your account has been disabled. Please contact with administrator.']);
        }
        return $next($request);
    }
}
