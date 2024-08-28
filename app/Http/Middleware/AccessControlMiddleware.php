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

class AccessControlMiddleware
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
        $key = false;
        $pass = false;


        header("Access-Control-Allow-Origin: *");

        $headers = [
            'Access-Control-Allow-Methods' => 'POST,GET,OPTIONS,PUT,DELETE',
            'Access-Control-Allow-Headers' => 'Content-Type, X-Auth-Token, Origin, Authorization',
        ];
        if ($request->getMethod() == "OPTIONS") {

            return response()->json('OK',200,$headers);
        }
        $response = $next($request);
        foreach ($headers as $key => $value) {
            $response->header($key, $value);
        }

        return $response;
    }

}
