<?php

namespace App\Http\Middleware;

use App\AnotherClasses\ResponseHandler;
use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
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
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return ResponseHandler::getJsonResponse(400, "Невалидный токен");
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return ResponseHandler::getJsonResponse(400, "Токен устарел");
            }else{
                return ResponseHandler::getJsonResponse(400, "Токен не был передан");
            }
        }
        return $next($request);
    }
}