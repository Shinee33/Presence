<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;

class RedirectIfAuthenticated
{
    public function handle($request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard('karyawan')->check()) {
                if (request()->is('panel/*')) {
                    return redirect(RouteServiceProvider::HOME);
                } 
                if (Auth::guard('user')->check()) {
                    return redirect(RouteServiceProvider::HOMEADMIN);
                
            }
        }

        return $next($request);
    }
}
}