<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Redis;
use Closure;
use Auth;

class LastUserActivity
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
        if (Auth::check()) {
            // set expire time 60 seconds
            $expireTime = 60;
            Redis::set('user-is-online-' . Auth::user()->id, true, 'EX', $expireTime);
        }
        return $next($request);
    }
}