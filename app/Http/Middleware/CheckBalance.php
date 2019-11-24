<?php

namespace App\Http\Middleware;

use Closure;
use App\Transaction;


class CheckBalance
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
      if (!empty(session('user_id'))) {
        $usrBalance = Transaction::gatBalance(session('user_id'));
        session(['balance' => $usrBalance]);
      }
      return $next($request);
    }
}
