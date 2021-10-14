<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;
use App\Tools;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        // if ($this->authenticate($request, $guards) === 'authentication_error') {
        //     return response()->json(['error'=>'Unauthorized']);
        // }
        // return $next($request);
        if(Auth::user()) {
            return $next($request);
        }
        else {
            return response()->json(['error' => 'Unauthorizedthos'], 403);
        }
    }

    protected function authenticate($request, array $guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }
        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                return $this->auth->shouldUse($guard);
            }
        }
        return 'authentication_error';
    }

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }
}
