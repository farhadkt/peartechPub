<?php

namespace App\Http\Middleware;

use Closure;

class IsActiveUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check() && !auth()->user()->isActive()) {
            \Auth::logout();
            return redirect()->back();
        }

        return $next($request);
    }
}
