<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use Carbon\Carbon;

class userInfo
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
        // $request->ip = '151.101.65.121';


        if ($request->path() != '/') {
          if (empty($request->cookie('remember_token'))) {
            return redirect('/');
          }

          if (!User::sessionValid()) {
            $logResp = User::logAgain($request);
            if (!$logResp) {
              return redirect('/');
            }
          }
        }
        return $next($request);
    }
}
