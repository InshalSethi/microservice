<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;


class AdminRedirectAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token has expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token is invalid'], 401);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token is missing or invalid'], 401);
        } catch (TokenExpiredException $e) {
            // Invalidate the old token
            $token = JWTAuth::getToken();
            JWTAuth::invalidate($token);




            // Return an error response with instructions for obtaining a new token
            return response()->json([
                'error' => 'Token has expired',
                'message' => 'Please obtain a new token from the authentication endpoint.'
            ], 401);
        }



        // // Check if the user has the required role or permissions
        // if (!$user->hasRole('admin') && !$user->hasPermission('access_admin_panel')) {
        //     return response()->json(['error' => 'Unauthorized'], 403);
        // }

        return $next($request);
    }
}
