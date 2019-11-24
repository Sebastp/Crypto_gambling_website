<?php

namespace App\Http\Middleware;

use App\User;

use Closure;

class LogUserActivity
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
        User::setActiveUsr(session('user_id'));
        return $next($request);
    }
}
