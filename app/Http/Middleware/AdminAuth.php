<?php

namespace App\Http\Middleware;

use Closure;
use App\Model\ProcessLog;
use Carbon\Carbon;
use App\Classes\Constant;
use Illuminate\Auth\AuthenticationException;
use App\Model\Client;
use App\Model\JsToken;
use Illuminate\Support\Facades\Auth;
use App\User;

class AdminAuth
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
        Auth::setDefaultDriver('admin');
        return $next($request);
    }
    
}
