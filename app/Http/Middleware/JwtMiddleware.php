<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use App\User;

class JwtMiddleware
{
	private $token = '';
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if ($request->get('token')) $this->token = $request->get('token');
		else if($request->header('Authorization')) $this->token = substr($request->header('Authorization'), 7);

		if (!$this->token) return response()->json(['message' => 'Token not provided'], 401);

		try {
				$credentials = JWT::decode($this->token, env('JWT_SECRET'), ['HS256']);
		} catch (ExpiredException $e) {
				return response()->json([
						'message' => 'Token expired, please login again'
				], 401);
		} catch (SignatureInvalidException $e) {
				return response()->json(['message' => 'Invalid token provided'], 401);
		}

		$user = User::find($credentials->sub);
		$request->auth = $user;
		return $next($request);
	}
}
