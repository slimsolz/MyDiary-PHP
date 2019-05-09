<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use App\User;

class JwtMiddleware
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
        $token = '';
        if ($request->get('token')) {
            $token = $request->get('token');
        }
        else if ($request->header('Authorization')){
            $token = substr($request->header('Authorization'), 7);
        }

        if (!$token) {
            return response()->json([
                'message' => 'Token not provided'
            ], 401);
        }

        try {
            $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
        } catch (ExpiredException $e) {
            return response()->json([
                'message' => 'Token expired, please login again'
            ], 401);
        } catch (SignatureInvalidException $e) {
            return response()->json([
                'message' => 'Invalid token provided'
            ], 401);
        }

        $user = User::find($credentials->sub);
        $request->auth = $user;
        return $next($request);
    }
}
