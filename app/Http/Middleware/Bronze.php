<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;
use JWTAuth;

class Bronze
{
    private $auth;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $this->auth =
            $user ?
                ($user->member_id === 2)
                : false;

        if($this->auth === true)
            return $next($request);
        else{
            abort(403);
        }
    }
}
