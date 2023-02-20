<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class AuthLogin
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
        if (!Auth::user()) {
            if(isset($_COOKIE["buker_cookie"])){
                $cookies = htmlspecialchars($_COOKIE["buker_cookie"]);
                if($cookies){
                    return redirect(url('login-attempt?cookies='.$cookies));
                }
            }
            return redirect(url('landing'));
        }
        return $next($request);
    }
}
